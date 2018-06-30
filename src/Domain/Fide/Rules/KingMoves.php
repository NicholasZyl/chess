<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\CastlingPrevented;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Chessboard;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;

final class KingMoves implements Rule
{
    private const MOVE_TO_ADJOINING_SQUARE = 1;
    private const CASTLING_MOVE_DISTANCE = 2;
    private const INITIAL_ROOK_POSITIONS = [
        'a1',
        'h1',
        'a8',
        'h8',
    ];

    /**
     * @var \SplObjectStorage
     */
    private $movedKings;

    /**
     * @var string[]
     */
    private $rookPositionsAvailableForCastling;

    /**
     * Create Castling Move rules.
     */
    public function __construct()
    {
        $this->movedKings = new \SplObjectStorage();
        $this->rookPositionsAvailableForCastling = array_flip(self::INITIAL_ROOK_POSITIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function applyAfter(Event $event, Board $board, Rules $rules): array
    {
        if ($event instanceof Event\PieceWasMoved) {
            return $this->onPieceMoved($event, $board);
        } elseif ($event instanceof Event\PieceWasCaptured) {
            if ($event->piece() instanceof Rook) {
                unset($this->rookPositionsAvailableForCastling[(string)$event->capturedAt()]);
            }
        }

        return [];
    }

    /**
     * Handle an event that piece has moved.
     *
     * @param Event\PieceWasMoved $event
     * @param Board $board
     *
     * @return Event[]
     */
    private function onPieceMoved(Event\PieceWasMoved $event, Board $board)
    {
        $events = [];

        if ($event->piece() instanceof King) {
            $this->movedKings->attach($event->piece());
            if ($event->wasOverDistanceOf(self::CASTLING_MOVE_DISTANCE)) {
                $events = $this->onCastlingKingMoved($event, $board);
            }
        } elseif ($event->piece() instanceof Rook) {
            unset($this->rookPositionsAvailableForCastling[(string)$event->source()]);
        }

        return $events;
    }

    /**
     * Handle an event that king just did his part of castling move.
     *
     * @param Event\PieceWasMoved $event
     * @param Board $board
     *
     * @return Event[]
     */
    private function onCastlingKingMoved(Event\PieceWasMoved $event, Board $board)
    {
        $rookPosition = $this->getExpectedRookPosition($event->source(), $event->destination());
        $rookDestination = $event->destination()->nextTowards($event->source(), new AlongRank());

        $rook = $board->pickPieceFrom($rookPosition);
        $board->placePieceAt($rook, $rookDestination);

        return [new Event\PieceWasMoved(new Move($rook, $rookPosition, $rookDestination)),];
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(Action $action): bool
    {
        return $action instanceof Move && $action->piece() instanceof King;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if (!$action instanceof Move) {
            throw new RuleIsNotApplicable();
        }

        if (!$this->isApplicable($action)) {
            throw new MoveToIllegalPosition($action);
        }

        if (!$action->inKnownDirection()) {
            throw new MoveToIllegalPosition($action);
        }

        if ($action->isOverDistanceOf(self::CASTLING_MOVE_DISTANCE) && $action->inDirection(new AlongRank())) {
            $this->applyToCastling($action, $board, $rules);
        } else if (!$action->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE)) {
            throw new MoveToIllegalPosition($action);
        }
    }

    /**
     * Apply rule to a castling move.
     *
     * @param Move $move
     * @param Board $board
     * @param Rules $rules
     *
     * @throws IllegalAction
     *
     * @return void
     */
    private function applyToCastling(Move $move, Board $board, Rules $rules): void
    {
        if ($this->movedKings->contains($move->piece())) {
            throw new MoveToIllegalPosition($move);
        }

        $rookPosition = $this->getExpectedRookPosition($move->source(), $move->destination());
        if (!array_key_exists((string)$rookPosition, $this->rookPositionsAvailableForCastling)) {
            throw new MoveToIllegalPosition($move);
        }

        $alongRank = new AlongRank();
        $step = $move->source()->nextTowards($rookPosition, $alongRank);
        while (!$step->equals($rookPosition)) {
            if ($board->isPositionOccupied($step)) {
                throw new CastlingPrevented($move);
            }
            $step = $step->nextTowards($rookPosition, $alongRank);
        }

        $step = $move->source();
        $destination = $move->destination();
        while (!$step->equals($destination)) {
            if ($board->isPositionAttackedBy($step, $move->piece()->color()->opponent(), $rules)) {
                throw new CastlingPrevented($move);
            }
            $step = $step->nextTowards($destination, $alongRank);
        }
        if ($board->isPositionAttackedBy($step, $move->piece()->color()->opponent(), $rules)) {
            throw new CastlingPrevented($move);
        }
    }

    /**
     * @param $source
     * @param $destination
     *
     * @return CoordinatePair
     */
    private function getExpectedRookPosition(Coordinates $source, Coordinates $destination): CoordinatePair
    {
        return CoordinatePair::fromFileAndRank(
            $source->file() < $destination->file() ? Chessboard::FILE_MOST_KINGSIDE : Chessboard::FILE_MOST_QUEENSIDE,
            $source->rank()
        );
    }
}
