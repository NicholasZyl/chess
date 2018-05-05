<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard\Exception\NotPermittedMove;
use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

interface Board
{
    /**
     * Place a piece at given coordinates.
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     *
     * @return void
     */
    public function placePieceAtCoordinates(Piece $piece, CoordinatePair $coordinates): void;

    /**
     * Move a piece from one position to another.
     *
     * @param Move $move
     *
     * @throws NotPermittedMove
     *
     * @return void
     */
    public function movePiece(Move $move): void;

    /**
     * Verify that given position is unoccupied.
     *
     * @param CoordinatePair $position
     *
     * @throws SquareIsOccupied
     *
     * @return void
     */
    public function verifyThatPositionIsUnoccupied(CoordinatePair $position);

    /**
     * Check if same piece is already placed on square at given coordinates.
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     *
     * @return bool
     */
    public function hasPieceAtCoordinates(Piece $piece, CoordinatePair $coordinates): bool;
}
