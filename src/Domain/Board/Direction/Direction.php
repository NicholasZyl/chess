<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;

interface Direction
{
    /**
     * Calculate next coordinates towards destination in this direction.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @throws InvalidDirection
     *
     * @return Coordinates
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates;

    /**
     * Get string representation of the direction.
     *
     * @return string
     */
    public function __toString(): string;
}