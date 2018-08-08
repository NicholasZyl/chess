<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Action;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Exception\Board\UnknownDirection;
use NicholasZyl\Chess\Domain\Piece;

abstract class Movement implements Action
{
    /**
     * @var Piece
     */
    private $piece;

    /**
     * @var Coordinates
     */
    private $source;

    /**
     * @var Coordinates
     */
    private $destination;

    /**
     * @var Color
     */
    private $player;

    /**
     * Create a piece move from one position to another.
     *
     * @param Piece $piece
     * @param Coordinates $source
     * @param Coordinates $destination
     */
    public function __construct(Piece $piece, Coordinates $source, Coordinates $destination)
    {
        $this->piece = $piece;
        $this->source = $source;
        $this->destination = $destination;
        $this->player = $piece->color();
    }

    /**
     * {@inheritdoc}
     */
    public function player(): Color
    {
        return $this->player;
    }

    /**
     * What piece is moving.
     *
     * @return Piece
     */
    public function piece(): Piece
    {
        return $this->piece;
    }

    /**
     * Get the position from piece is moving.
     *
     * @return Coordinates
     */
    public function source(): Coordinates
    {
        return $this->source;
    }

    /**
     * Get the position piece is moving to.
     *
     * @return Coordinates
     */
    public function destination(): Coordinates
    {
        return $this->destination;
    }

    /**
     * Check if move is along given direction.
     *
     * @param Direction $direction
     *
     * @return bool
     */
    public function inDirection(Direction $direction): bool
    {
        return $direction->areOnSame($this->source, $this->destination);
    }

    /**
     * Check if move is made over expected distance.
     *
     * @param int $expectedDistance
     *
     * @return bool
     */
    public function isOverDistanceOf(int $expectedDistance): bool
    {
        try {
            return $this->source->distanceTo($this->destination, $this->source->directionTo($this->destination)) === $expectedDistance;
        } catch (UnknownDirection $unknownDirection) {
            return false;
        }
    }
}