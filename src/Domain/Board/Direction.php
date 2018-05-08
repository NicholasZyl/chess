<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Exception\InvalidDirection;

interface Direction
{
    /**
     * Check if two coordinates are on same direction.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @return bool
     */
    public function areOnSame(Coordinates $from, Coordinates $to): bool;

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