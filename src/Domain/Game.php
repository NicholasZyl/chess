<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\BoardException;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;
use NicholasZyl\Chess\Domain\Piece\InitialPositions;

class Game
{
    /**
     * @var Board
     */
    private $board;

    /**
     * @var Rules
     */
    private $rules;

    /**
     * Create a new game.
     *
     * @param Board $board
     * @param InitialPositions $initialPositions
     * @param Rules $rules
     */
    public function __construct(Board $board, InitialPositions $initialPositions, Rules $rules)
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
            $this->rules->applyRulesTo($move, $this->board);
            $events = $this->board->placePieceAt($piece, $to);
            $events[] = new PieceWasMoved($move);

            $events = array_merge($events, $this->onEventsOccurred($events));
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

        return $events;
    }

    /**
     * Act on events that just occurred.
     *
     * @param array $events
     *
     * @throws IllegalAction\MoveExposesToCheck
     *
     * @return Event[]
     */
    private function onEventsOccurred(array $events): array
    {
        $firedEvents = [];
        foreach ($events as $event) {
            $firedEvents = array_merge($firedEvents, $this->rules->applyAfter($event, $this->board));
        }

        return $firedEvents;
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
            $this->rules->applyRulesTo($exchange, $this->board);

            return $this->board->exchangePieceOnPositionTo($position, $exchangedPiece);
        } catch (IllegalAction | BoardException $exception) {
            throw new ExchangeIsNotAllowed($position);
        }
    }
}
