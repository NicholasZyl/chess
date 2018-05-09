<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Move\TooDistant;

final class ToAdjoiningSquare implements BoardMove
{
    private const DISTANCE_TO_ADJOINING_SQUARE = 1;

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
     * Create move to an adjoining square.
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
    public function __toString(): string
    {
        return sprintf('move to an adjoining square %s', $this->direction);
    }

    /**
     * {@inheritdoc}
     */
    public function play(Board $board): void
    {
        $distanceAlongFile = abs(ord($this->source()->file()) - ord($this->destination()->file()));
        $distanceAlongRank = abs($this->source()->rank() - $this->destination()->rank());
        if ($distanceAlongFile > self::DISTANCE_TO_ADJOINING_SQUARE || $distanceAlongRank > self::DISTANCE_TO_ADJOINING_SQUARE) {
            throw new TooDistant($this);
        }

        $piece = $board->pickPieceFromCoordinates($this->source);
        $piece->canMove($this);
        $board->placePieceAtCoordinates($piece, $this->destination);
    }

    /**
     * {@inheritdoc}
     */
    public function is(string $moveType): bool
    {
        return $this instanceof $moveType;
    }

    /**
     * {@inheritdoc}
     */
    public function inDirection(Board\Direction $direction): bool
    {
        return $this->direction->inSameDirectionAs($direction);
    }
}
