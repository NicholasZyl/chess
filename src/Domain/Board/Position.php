<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Piece;

interface Position
{
    /**
     * Place piece on the position.
     *
     * @param Piece $piece
     *
     * @throws SquareIsOccupied
     */
    public function place(Piece $piece): void;

    /**
     * Pick a piece from the position.
     *
     * @throws SquareIsUnoccupied
     *
     * @return Piece
     */
    public function pick(): Piece;

    /**
     * Verify that the position is unoccupied by any piece.
     *
     * @throws SquareIsOccupied
     *
     * @return void
     */
    public function verifyThatUnoccupied(): void;

    /**
     * Check if same piece is already placed on the position.
     *
     * @param Piece $piece
     *
     * @return bool
     */
    public function hasPlacedPiece(Piece $piece): bool;
}