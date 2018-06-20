<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoardCoordinates;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
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
     * @throws SquareIsOccupied
     *
     * @return Event[]
     */
    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): array;

    /**
     * Pick a piece from given coordinates.
     *
     * @param Coordinates $coordinates
     *
     * @throws OutOfBoardCoordinates
     * @throws SquareIsUnoccupied
     *
     * @return Piece
     */
    public function pickPieceFromCoordinates(Coordinates $coordinates): Piece;

    /**
     * Check if given position is occupied.
     *
     * @param Coordinates $position
     *
     * @throws OutOfBoardCoordinates
     *
     * @return bool
     */
    public function isPositionOccupied(Coordinates $position): bool;

    /**
     * Check if given position is occupied by piece of color different than passed one.
     *
     * @param Coordinates $coordinates
     * @param Color $pieceColor
     *
     * @throws OutOfBoardCoordinates
     *
     * @return bool
     */
    public function isPositionOccupiedByOpponentOf(Coordinates $coordinates, Color $pieceColor): bool;

    /**
     * Check if position is attacked by any piece of passed color according to the game and its rules.
     *
     * @param Coordinates $position
     * @param Color $color
     * @param Game $game
     *
     * @throws OutOfBoardCoordinates
     *
     * @return bool
     */
    public function isPositionAttackedBy(Coordinates $position, Color $color, Game $game): bool;

    /**
     * Remove a piece from given position.
     *
     * @param Coordinates $position
     *
     * @throws OutOfBoardCoordinates
     * @throws SquareIsUnoccupied
     *
     * @return Piece
     */
    public function removePieceFrom(Coordinates $position): Piece;
}
