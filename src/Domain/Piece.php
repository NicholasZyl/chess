<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
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
     * Compare if piece has the same color as another one;
     *
     * @param Piece $anotherPiece
     *
     * @return bool
     */
    public function isSameColorAs(Piece $anotherPiece): bool;

    /**
     * Compare if piece has the same rank and color as another one.
     *
     * @param Piece $anotherPiece
     *
     * @return bool
     */
    public function isSameAs(Piece $anotherPiece): bool;

    /**
     * Check if intended move is legal for this piece.
     *
     * @param Move $move
     * @param Board $board
     *
     * @throws IllegalMove
     *
     * @return void
     */
    public function mayMove(Move $move, Board $board): void;

    /**
     * Represent piece as a string.
     *
     * @return string
     */
    public function __toString(): string;
}
