<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;

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
     * @throws CoordinatesNotReachable
     *
     * @return Coordinates
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates;

    /**
     * Calculates distance between two coordinates along this direction.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @throws CoordinatesNotReachable
     *
     * @return int
     */
    public function distanceBetween(Coordinates $from, Coordinates $to): int;

    /**
     * Get string representation of the direction.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Get next coordinates from given position along the direction.
     *
     * @param Coordinates $position
     *
     * @throws OutOfBoard
     *
     * @return Coordinates
     */
    public function nextAlongFrom(Coordinates $position): Coordinates;
}