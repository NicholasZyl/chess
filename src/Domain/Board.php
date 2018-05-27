<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoardCoordinates;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
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
     * @param Coordinates $source
     * @param Coordinates $destination
     *
     * @throws IllegalMove
     * @throws OutOfBoardCoordinates
     *
     * @return void
     */
    public function movePiece(Coordinates $source, Coordinates $destination): void;

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
     * Check if position is attacked by piece owned by opponent of passed color.
     *
     * @param Coordinates $coordinates
     * @param Color $color
     *
     * @return bool
     */
    public function isPositionAttackedByOpponentOf(Coordinates $coordinates, Color $color): bool;

    /**
     * Get all events that occurred after last move.
     *
     * @return Event[]
     */
    public function occurredEvents(): array;
}
