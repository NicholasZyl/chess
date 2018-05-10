<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\OutOfBoardCoordinates;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Piece\Color;

interface Board
{
    /**
     * Place a piece at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $coordinates
     *
     * @throws OutOfBoardCoordinates
     *
     * @return void
     */
    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): void;

    /**
     * Pick a piece from given coordinates.
     *
     * @param Coordinates $coordinates
     *
     * @throws SquareIsUnoccupied
     * @throws OutOfBoardCoordinates
     *
     * @return Piece
     */
    public function pickPieceFromCoordinates(Coordinates $coordinates): Piece;

    /**
     * Move a piece from one position to another.
     *
     * @param Move $move
     *
     * @throws IllegalMove
     * @throws OutOfBoardCoordinates
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
     * @throws OutOfBoardCoordinates
     *
     * @return void
     */
    public function verifyThatPositionIsUnoccupied(Coordinates $position);

    /**
     * Check if given position is occupied by piece of color different than passed one.
     *
     * @param Coordinates $coordinates
     * @param Color $pieceColor
     *
     * @return bool
     */
    public function hasOpponentsPieceAt(Coordinates $coordinates, Color $pieceColor): bool;

    /**
     * Check if same piece is already placed on square at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $coordinates
     *
     * @throws OutOfBoardCoordinates
     *
     * @return bool
     */
    public function hasPieceAtCoordinates(Piece $piece, Coordinates $coordinates): bool;
}
