<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

interface Coordinates
{
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
}