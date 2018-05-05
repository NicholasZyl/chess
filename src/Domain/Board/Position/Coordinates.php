<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board\Position;

interface Coordinates
{
    /**
     * Represent coordinates as string.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Compare if is the same as other pair.
     *
     * @param Coordinates $other
     *
     * @return bool
     */
    public function equals(Coordinates $other): bool;
}