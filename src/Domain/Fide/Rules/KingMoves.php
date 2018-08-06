<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Attack;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\CastlingPrevented;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Chessboard;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;

final class KingMoves implements PieceMovesRule
{
    private const CASTLING_MOVE_DISTANCE = 2;

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
     * @param array $initialRookPositions
     */
    public function __construct(array $initialRookPositions)
    {
        $this->movedKings = new \SplObjectStorage();
        $this->rookPositionsAvailableForCastling = array_flip(array_map(function (Coordinates $position) {
            return (string)$position;
        }, $initialRookPositions));
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
    public function isApplicableTo(Action $action): bool
    {
        return $action instanceof Move && $action->piece() instanceof King;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if (!$this->isApplicableTo($action)) {
            throw new RuleIsNotApplicable();
        }
        /** @var Move $action */
        if (!in_array($action->destination(), iterator_to_array($this->getLegalDestinationsFrom($action->piece(), $action->source(), $board)))) {
            throw new MoveToIllegalPosition($action);
        }

        if ($action instanceof Attack && $action->isOverDistanceOf(self::CASTLING_MOVE_DISTANCE)) {
            throw new MoveToIllegalPosition($action);
        }

        if ($action->isOverDistanceOf(self::CASTLING_MOVE_DISTANCE) && $action->inDirection(new AlongRank())) {
            $this->applyToCastling($action, $board, $rules);
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
        $rookPosition = $this->getExpectedRookPosition($move->source(), $move->destination());
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

    /**
     * {@inheritdoc}
     */
    public function isFor(): string
    {
        return King::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLegalDestinationsFrom(Piece $piece, Coordinates $actualPosition, Board $board): \Generator
    {
        /** @var Board\Direction[] $directions */
        $directions = [
            new AlongFile(true),
            new AlongDiagonal(true, true),
            new AlongRank(true),
            new AlongDiagonal(true, false),
            new AlongFile(false),
            new AlongDiagonal(false, false),
            new AlongRank(false),
            new AlongDiagonal(false, true),
        ];

        foreach ($directions as $direction) {
            try {
                $destination = $direction->nextAlongFrom($actualPosition);
                if (!$board->isPositionOccupiedBy($destination, $piece->color())) {
                    yield $destination;
                }
            } catch (OutOfBoard $outOfBoard) {
                continue;
            }
        }
        if ($actualPosition->equals($this->getStartingPositionForKing($piece)) && !$this->movedKings->contains($piece)) {
            if (array_key_exists(Chessboard::FILE_MOST_QUEENSIDE . $actualPosition->rank(), $this->rookPositionsAvailableForCastling)) {
                yield CoordinatePair::fromFileAndRank('c', $actualPosition->rank());
            }
            if (array_key_exists(Chessboard::FILE_MOST_KINGSIDE . $actualPosition->rank(), $this->rookPositionsAvailableForCastling)) {
                yield CoordinatePair::fromFileAndRank('g', $actualPosition->rank());
            }
        }
    }

    private function getStartingPositionForKing(Piece $king): Coordinates
    {
        return CoordinatePair::fromFileAndRank('e', $king->color()->is(Color::white()) ? Chessboard::LOWEST_RANK : Chessboard::HIGHEST_RANK);
    }
}
