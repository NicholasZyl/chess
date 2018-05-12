<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\Move\Prevented;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;

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
            throw new InvalidDirection($source, $destination, $this->direction);
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
    public function inDirection(Direction $direction): bool
    {
        return $this->direction->inSameDirectionAs($direction);
    }

    /**
     * {@inheritdoc}
     */
    public function play(Board $board): void
    {
        if (!$board->hasPieceAtCoordinates(King::forColor($this->color), $this->kingPosition) || !$board->hasPieceAtCoordinates(Rook::forColor($this->color), $this->rookPosition)) {
            throw new Prevented($this);
        }

        $king = $board->pickPieceFromCoordinates($this->kingPosition);
        $rook = $board->pickPieceFromCoordinates($this->rookPosition);

        try {
            $king->mayMove($this, $board);
            $rook->mayMove($this, $board);
            $this->validateSquaresBetweenKingAndRookAreUnoccupied($board);
        } catch (SquareIsOccupied | IllegalMove $squareIsOccupied) {
            $board->placePieceAtCoordinates($king, $this->kingPosition);
            $board->placePieceAtCoordinates($rook, $this->rookPosition);
            throw new Prevented($this);
        }

        $board->placePieceAtCoordinates($king, $this->kingDestination);
        $board->placePieceAtCoordinates($rook, $this->kingDestination->nextTowards($this->kingPosition, $this->direction));
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
    public function __toString(): string
    {
        return sprintf(
            '%s %s castling',
            $this->color,
            $this->side === self::QUEENSIDE ? 'queenside' : 'kingside'
        );
    }
}
