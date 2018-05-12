<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Move;

final class OverOtherPieces implements Move
{
    /**
     * @var Coordinates
     */
    private $source;

    /**
     * @var Coordinates
     */
    private $destination;

    /**
     * @var Board\Direction
     */
    private $direction;

    /**
     * Create move that cannot be done over any intervening pieces.
     *
     * @param Coordinates $source
     * @param Coordinates $destination
     * @param Board\Direction $direction
     *
     * @throws InvalidDirection
     */
    public function __construct(Coordinates $source, Coordinates $destination, Board\Direction $direction)
    {
        if (!$direction->areOnSame($source, $destination)) {
            throw new InvalidDirection($source, $destination, $direction);
        }

        $this->source = $source;
        $this->destination = $destination;
        $this->direction = $direction;
    }

    /**
     * {@inheritdoc}
     */
    public function source(): Coordinates
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function destination(): Coordinates
    {
        return $this->destination;
    }

    /**
     * {@inheritdoc}
     */
    public function inDirection(Board\Direction $direction): bool
    {
        return $this->direction->inSameDirectionAs($direction);
    }

    /**
     * {@inheritdoc}
     */
    public function play(Board $board): void
    {
        $piece = $board->pickPieceFromCoordinates($this->source);
        $piece->mayMove($this, $board);
        try {
            $board->placePieceAtCoordinates($piece, $this->destination);
        } catch (SquareIsOccupied $squareIsOccupied) {
            $board->placePieceAtCoordinates($piece, $this->source);
            throw new MoveToOccupiedPosition($squareIsOccupied->coordinates());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return "move over other pieces";
    }
}
