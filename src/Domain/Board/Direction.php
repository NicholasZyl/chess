<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;

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
     * Calculates distance between two coordinates along this direction.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @throws InvalidDirection
     *
     * @return int
     */
    public function distanceBetween(Coordinates $from, Coordinates $to): int;

    /**
     * Check if is in same direction as another.
     *
     * @param Direction $direction
     *
     * @return bool
     */
    public function inSameDirectionAs(Direction $direction): bool;

    /**
     * Get string representation of the direction.
     *
     * @return string
     */
    public function __toString(): string;
}