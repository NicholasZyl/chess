<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\UnknownDirection;

interface Coordinates
{
    /**
     * Get file coordinate.
     *
     * @return string
     */
    public function file(): string;

    /**
     * Get rank coordinate.
     *
     * @return int
     */
    public function rank(): int;

    /**
     * Calculate next adjacent coordinates toward destination in given direction.
     *
     * @param Coordinates $destination
     * @param Direction $direction
     *
     * @throws InvalidDirection
     *
     * @return Coordinates
     */
    public function nextTowards(Coordinates $destination, Direction $direction): Coordinates;

    /**
     * Get direction to another coordinates.
     *
     * @param Coordinates $coordinates
     *
     * @throws UnknownDirection
     *
     * @return Direction
     */
    public function directionTo(Coordinates $coordinates): Direction;

    /**
     * Compare if is the same as other pair.
     *
     * @param Coordinates $other
     *
     * @return bool
     */
    public function equals(Coordinates $other): bool;

    /**
     * Represent coordinates as string.
     *
     * @return string
     */
    public function __toString(): string;
}