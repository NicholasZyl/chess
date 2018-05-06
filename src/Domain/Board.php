<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\OutOfBoardPosition;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;

interface Board
{
    /**
     * Place a piece at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $coordinates
     *
     * @throws OutOfBoardPosition
     *
     * @return void
     */
    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): void;

    /**
     * Move a piece from one position to another.
     *
     * @param Move $move
     *
     * @throws IllegalMove
     * @throws OutOfBoardPosition
     *
     * @return void
     */
    public function movePiece(Move $move): void;

    /**
     * Verify that given position is unoccupied.
     *
     * @param Coordinates $position
     *
     * @throws SquareIsOccupied
     * @throws OutOfBoardPosition
     *
     * @return void
     */
    public function verifyThatPositionIsUnoccupied(Coordinates $position);

    /**
     * Check if same piece is already placed on square at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $coordinates
     *
     * @throws OutOfBoardPosition
     *
     * @return bool
     */
    public function hasPieceAtCoordinates(Piece $piece, Coordinates $coordinates): bool;
}
