<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveTooDistant;

final class AdvancingTwoSquares extends NotIntervened
{
    const ALLOWED_DISTANCE = 2;

    /**
     * Create move by at most two squares.
     *
     * @param Coordinates $source
     * @param Coordinates $destination
     * @param Direction $direction
     *
     * @throws MoveTooDistant
     * @throws InvalidDirection
     */
    public function __construct(Coordinates $source, Coordinates $destination, Board\Direction $direction)
    {
        $distanceAlongFile = abs(ord($source->file()) - ord($destination->file()));
        $distanceAlongRank = abs($source->rank() - $destination->rank());
        if ($distanceAlongFile > self::ALLOWED_DISTANCE || $distanceAlongRank > self::ALLOWED_DISTANCE) {
            throw new MoveTooDistant($source, $destination, self::ALLOWED_DISTANCE);
        }

        parent::__construct($source, $destination, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('%s by at most two squares', parent::__toString());
    }
}
