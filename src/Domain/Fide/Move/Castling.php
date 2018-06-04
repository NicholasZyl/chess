<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MovePrevented;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules;

final class Castling implements Move
{
    private const QUEENSIDE = -1;
    private const KINGSIDE = 1;

    /**
     * @var Coordinates
     */
    private $kingPosition;

    /**
     * @var Coordinates
     */
    private $kingDestination;

    /**
     * @var Direction
     */
    private $direction;

    /**
     * @var int
     */
    private $side;

    /**
     * @var CoordinatePair
     */
    private $rookPosition;

    /**
     * @var Color
     */
    private $color;

    /**
     * Create castling move.
     *
     * @param Color $color
     * @param Coordinates $source
     * @param Coordinates $destination
     */
    public function __construct(Color $color, Coordinates $source, Coordinates $destination)
    {
        $this->direction = new AlongRank();
        if (!$this->direction->areOnSame($source, $destination)) {
            throw new CoordinatesNotReachable($source, $destination, $this->direction);
        }

        $this->color = $color;
        $this->kingPosition = $source;
        $this->kingDestination = $destination;
        $this->side = $source->file() < $destination->file() ? self::KINGSIDE : self::QUEENSIDE;
        $this->rookPosition = CoordinatePair::fromFileAndRank($this->side === self::QUEENSIDE ? 'a' : 'h', $source->rank());
    }

    /**
     * {@inheritdoc}
     */
    public function source(): Coordinates
    {
        return $this->kingPosition;
    }

    /**
     * {@inheritdoc}
     */
    public function destination(): Coordinates
    {
        return $this->kingDestination;
    }

    /**
     * {@inheritdoc}
     */
    public function isOverDistanceOf(int $expectedDistance): bool
    {
        return $this->kingPosition->distanceTo($this->kingDestination, $this->direction) === $expectedDistance;
    }

    /**
     * {@inheritdoc}
     */
    public function inDirection(Direction $direction): bool
    {
        return $this->direction->inSameDirectionAs($direction);
    }

    /**
     * {@inheritdoc}
     */
    public function isLegal(Board $board): void
    {
        try {
            $this->validateSquaresBetweenKingAndRookAreUnoccupied($board);
        } catch (SquareIsOccupied $squareIsOccupied) {
            throw new MovePrevented($this);
        }

        $step = $this->kingPosition;
        if ($board->isPositionAttackedByOpponentOf($step, $this->color)) {
            throw new MovePrevented($this);
        }
        while (!$step->equals($this->kingDestination)) {
            $step = $step->nextTowards($this->kingDestination, $this->direction);
            if ($board->isPositionAttackedByOpponentOf($step, $this->color)) {
                throw new MovePrevented($this);
            }
        }
    }

    /**
     * Verify that all squares between pieces are unoccupied.
     *
     * @param Board $board
     *
     * @return void
     */
    private function validateSquaresBetweenKingAndRookAreUnoccupied(Board $board): void
    {
        $step = $this->kingPosition->nextTowards($this->rookPosition, $this->direction);
        while (!$step->equals($this->rookPosition)) {
            $board->verifyThatPositionIsUnoccupied($step);
            $step = $step->nextTowards($this->rookPosition, $this->direction);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function play(Board $board, Rules $rules): array
    {
        $this->isLegal($board);

        try {
            $king = $board->pickPieceFromCoordinates($this->kingPosition);
            $rook = $board->pickPieceFromCoordinates($this->rookPosition);
        } catch (SquareIsUnoccupied $exception) {
            if (isset($king)) {
                $board->placePieceAtCoordinates($king, $this->kingPosition);
            }
            if (isset($rook)) {
                $board->placePieceAtCoordinates($rook, $this->rookPosition);
            }
            throw new MovePrevented($this);
        }

        if (!$king->isSameAs(King::forColor($this->color)) || !$rook->isSameAs(Rook::forColor($this->color))) {
            $board->placePieceAtCoordinates($king, $this->kingPosition);
            $board->placePieceAtCoordinates($rook, $this->rookPosition);
            throw new MovePrevented($this);
        }

        try {
            $rules->mayMove($king, $this);
            $rules->mayMove($rook, $this);
        } catch (IllegalMove $illegalMove) {
            if (isset($king)) {
                $board->placePieceAtCoordinates($king, $this->kingPosition);
            }
            if (isset($rook)) {
                $board->placePieceAtCoordinates($rook, $this->rookPosition);
            }
            throw new MovePrevented($this);
        }

        $board->placePieceAtCoordinates($king, $this->kingDestination);
        $rookDestination = $this->kingDestination->nextTowards($this->kingPosition, $this->direction);
        $board->placePieceAtCoordinates($rook, $rookDestination);

        return [
            new PieceWasMoved($king, $this->kingPosition, $this->kingDestination),
            new PieceWasMoved($rook, $this->rookPosition, $rookDestination),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf(
            '%s %s castling',
            $this->color,
            $this->side === self::QUEENSIDE ? 'queenside' : 'kingside'
        );
    }
}
