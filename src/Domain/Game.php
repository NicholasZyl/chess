<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\BoardException;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\InitialPositions;

class Game
{
    /**
     * @var Board
     */
    private $board;

    /**
     * @var Rule[]
     */
    private $rules;

    /**
     * Create a new game.
     *
     * @param Board $board
     * @param InitialPositions $initialPositions
     * @param Rule[] $rules
     */
    public function __construct(Board $board, InitialPositions $initialPositions, array $rules)
    {
        $this->board = $board;
        $this->rules = $rules;
        $initialPositions->initialiseBoard($this->board);
    }

    /**
     * Play the move on the board from one position to another.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @throws BoardException
     * @throws IllegalAction
     *
     * @return Event[]
     */
    public function playMove(Coordinates $from, Coordinates $to): array
    {
        $piece = $this->board->pickPieceFrom($from);
        $move = new Move($piece, $from, $to);
        try {
            $this->applyRuleToAction($move);
            $events = $this->board->placePieceAt($piece, $to);
            $events[] = new PieceWasMoved($move);
            $events = array_merge($events, $this->onEventsOccurred($events));

            return $events;
        } catch (NoApplicableRule $noApplicableRule) {
            $this->board->placePieceAt($piece, $from);
            throw new MoveToIllegalPosition($move);
        } catch (IllegalAction $illegalMove) {
            $this->board->placePieceAt($piece, $from);
            throw $illegalMove;
        } catch (SquareIsOccupied $squareIsOccupied) {
            $this->board->placePieceAt($piece, $from);
            throw new MoveToOccupiedPosition($squareIsOccupied->coordinates());
        }
    }

    /**
     * Act on events that just occurred.
     *
     * @param array $events
     *
     * @return Event[]
     */
    private function onEventsOccurred(array $events): array
    {
        $firedEvents = [];
        foreach ($events as $event) {
            $firedEvents = array_merge($firedEvents, $this->applyRulesAfterEvent($event));
        }

        return $firedEvents;
    }

    /**
     * Apply all rules after an event happened.
     *
     * @param Event $event
     *
     * @return Event[]
     */
    private function applyRulesAfterEvent(Event $event): array
    {
        $events = [];
        foreach ($this->rules as $rule) {
            $events = array_merge($events, $rule->applyAfter($event, $this));
        }

        return $events;
    }

    /**
     * Check if given piece may move between these positions.
     *
     * @param Piece $piece
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @return bool
     */
    public function mayMove(Piece $piece, Coordinates $from, Coordinates $to): bool
    {
        try {
            $move = new Move($piece, $from, $to);
            $this->applyRuleToAction($move);

            return true;
        } catch (IllegalAction $illegalMove) {
            return false;
        }
    }

    /**
     * Is given position occupied by any piece.
     *
     * @param Coordinates $position
     *
     * @throws OutOfBoard
     *
     * @return bool
     */
    public function isPositionOccupied(Coordinates $position): bool
    {
        return $this->board->isPositionOccupied($position);
    }

    /**
     * Is given position occupied by a piece owned by opponent of given color.
     *
     * @param Coordinates $position
     * @param Color $color
     *
     * @throws OutOfBoard
     *
     * @return bool
     */
    public function isPositionOccupiedByOpponentOf(Coordinates $position, Color $color): bool
    {
        return $this->board->isPositionOccupiedBy($position, $color->opponent());
    }

    /**
     * Is given position attacked by any piece owned by opponent of given color.
     *
     * @param Coordinates $position
     * @param Color $color
     *
     * @throws OutOfBoard
     *
     * @return bool
     */
    public function isPositionAttackedByOpponentOf(Coordinates $position, Color $color): bool
    {
        return $this->board->isPositionAttackedBy($position, $color->opponent(), $this);
    }

    /**
     * Remove piece from the board at given position.
     *
     * @param Coordinates $position
     *
     * @throws OutOfBoard
     * @throws SquareIsUnoccupied
     *
     * @return Piece
     */
    public function removePieceFromBoard(Coordinates $position): Piece
    {
        return $this->board->removePieceFrom($position);
    }

    /**
     * Exchange a piece on the board at given position to provided piece.
     *
     * @param Coordinates $position
     * @param Piece $exchangedPiece
     *
     * @throws ExchangeIsNotAllowed
     *
     * @return Event[]
     */
    public function exchangePieceOnBoardTo(Coordinates $position, Piece $exchangedPiece): array
    {
        try {
            $exchange = new Exchange($exchangedPiece, $position);
            $this->applyRuleToAction($exchange);

            return $this->board->exchangePieceOnPositionTo($position, $exchangedPiece);
        } catch (IllegalAction | BoardException $exception) {
            throw new ExchangeIsNotAllowed($position);
        }
    }

    /**
     * Find and apply the most appropriate, applicable rule to given action.
     *
     * @param Action $action
     *
     * @throws NoApplicableRule
     * @throws IllegalAction
     *
     * @return void
     */
    private function applyRuleToAction(Action $action): void
    {
        $rules = array_filter(
            $this->rules,
            function (Rule $rule) use ($action) {
                return $rule->isApplicable($action);
            }
        );
        if (empty($rules)) {
            throw new NoApplicableRule();
        }
        usort($rules, function (Rule $ruleA, Rule $ruleB) {
            return $ruleB->priority() <=> $ruleA->priority();
        });

        /** @var Rule $rule */
        $rule = reset($rules);
        $rule->apply($action, $this);
    }
}
