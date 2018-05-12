<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Move\TooDistant;

final class ToAdjoiningSquare extends NotIntervened
{
    private const DISTANCE_TO_ADJOINING_SQUARE = 1;

    /**
     * Create move to an adjoining square.
     *
     * @param Coordinates $source
     * @param Coordinates $destination
     * @param Board\Direction $direction
     *
     * @throws InvalidDirection
     * @throws TooDistant
     */
    public function __construct(Coordinates $source, Coordinates $destination, Board\Direction $direction)
    {
        $distanceAlongFile = abs(ord($source->file()) - ord($destination->file()));
        $distanceAlongRank = abs($source->rank() - $destination->rank());
        if ($distanceAlongFile > self::DISTANCE_TO_ADJOINING_SQUARE || $distanceAlongRank > self::DISTANCE_TO_ADJOINING_SQUARE) {
            throw new TooDistant($source, $destination, self::DISTANCE_TO_ADJOINING_SQUARE);
        }

        parent::__construct($source, $destination, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('%s to an adjoining square', parent::__toString());
    }
}
