<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
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
}
