<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event\Checkmated;
use NicholasZyl\Chess\Domain\Event\GameEnded;
use NicholasZyl\Chess\Domain\Event\InCheck;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\BoardException;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;

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
     * @var bool
     */
    private $hasEnded = false;

    /**
     * @var Color|null
     */
    private $winner;

    /**
     * @var Color|null
     */
    private $checked;

    /**
     * Create a new game.
     *
     * @param Board $board
     * @param GameArrangement $arrangement
     */
    public function __construct(Board $board, GameArrangement $arrangement)
    {
        $this->board = $board;
        $this->rules = $arrangement->rules();
        $arrangement->initialiseBoard($this->board);
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

        return array_merge($events, $this->onEventsOccurred($events));
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
            $firedEvents = array_merge($firedEvents, $this->rules->applyAfter($event, $this->board));
            $this->onEventOccurred($event);
        }
        if ($firedEvents) {
            $firedEvents = array_merge($firedEvents, $this->onEventsOccurred($firedEvents));
        }

        return $firedEvents;
    }

    /**
     * Change game state if needed on event that just happened.
     *
     * @param Event $event
     *
     * @return void
     */
    private function onEventOccurred(Event $event): void
    {
        if ($event instanceof GameEnded) {
            $this->hasEnded = true;
            $this->winner = $event->winner();
        } elseif ($event instanceof InCheck || $event instanceof Checkmated) {
            $this->checked = $event->color();
        } elseif ($event instanceof PieceWasMoved) {
            $this->checked = null;
        }
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

    /**
     * Get the current representation of the board with pieces on it.
     *
     * @return Piece[][]
     */
    public function board(): array
    {
        $board = [];
        $grid = $this->board->grid();
        foreach ($grid as $square) {
            $coordinates = $square->coordinates();
            $board[$coordinates->file()][$coordinates->rank()] = $square->peek();
        }

        return $board;
    }

    /**
     * Has the game already ended.
     *
     * @return bool
     */
    public function hasEnded(): bool
    {
        return $this->hasEnded;
    }

    /**
     * Get the winner.
     *
     * @return Color|null
     */
    public function winner(): ?Color
    {
        return $this->winner;
    }

    /**
     * Get the player that's checked.
     *
     * @return Color|null
     */
    public function checked(): ?Color
    {
        return $this->checked;
    }
}
