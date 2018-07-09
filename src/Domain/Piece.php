<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Piece\Color;

interface Piece
{
    /**
     * Get piece's color.
     *
     * @return Color
     */
    public function color(): Color;

    /**
     * Check if piece has given color.
     *
     * @param Color $color
     *
     * @return bool
     */
    public function hasColor(Color $color): bool;

    /**
     * Compare if piece has the same rank and color as another one.
     *
     * @param Piece $anotherPiece
     *
     * @return bool
     */
    public function isSameAs(Piece $anotherPiece): bool;

    /**
     * Represent piece as a string.
     *
     * @return string
     */
    public function __toString(): string;
}
