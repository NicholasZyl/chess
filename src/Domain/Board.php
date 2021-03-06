<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Square;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\Board\PositionOccupiedByAnotherColor;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;

interface Board
{
    /**
     * Place a piece at given position.
     *
     * @param Piece $piece
     * @param Coordinates $position
     *
     * @throws OutOfBoard
     * @throws SquareIsOccupied
     *
     * @return Event[]
     */
    public function placePieceAt(Piece $piece, Coordinates $position): array;

    /**
     * Pick a piece from given position.
     *
     * @param Coordinates $position
     *
     * @throws OutOfBoard
     * @throws SquareIsUnoccupied
     *
     * @return Piece
     */
    public function pickPieceFrom(Coordinates $position): Piece;

    /**
     * Check if given position is occupied.
     *
     * @param Coordinates $position
     *
     * @throws OutOfBoard
     *
     * @return bool
     */
    public function isPositionOccupied(Coordinates $position): bool;

    /**
     * Check if given position is occupied by piece of given color.
     *
     * @param Coordinates $position
     * @param Color $color
     *
     * @throws OutOfBoard
     *
     * @return bool
     */
    public function isPositionOccupiedBy(Coordinates $position, Color $color): bool;

    /**
     * Check if position is attacked by any piece of passed color according to the rules.
     *
     * @param Coordinates $position
     * @param Color $color
     * @param Rules $rules
     *
     * @throws OutOfBoard
     *
     * @return bool
     */
    public function isPositionAttackedBy(Coordinates $position, Color $color, Rules $rules): bool;

    /**
     * Remove a piece from given position.
     *
     * @param Coordinates $position
     *
     * @throws OutOfBoard
     * @throws SquareIsUnoccupied
     *
     * @return Piece
     */
    public function removePieceFrom(Coordinates $position): Piece;

    /**
     * Exchange piece placed on position to the provided one.
     *
     * @param Coordinates $position
     * @param Piece $exchangedPiece
     *
     * @throws OutOfBoard
     * @throws PositionOccupiedByAnotherColor
     *
     * @return Event[]
     */
    public function exchangePieceOnPositionTo(Coordinates $position, Piece $exchangedPiece): array;

    /**
     * Check if given color has any legal move.
     *
     * @param Color $color
     * @param Rules $rules
     *
     * @return bool
     */
    public function hasLegalMove(Color $color, Rules $rules): bool;

    /**
     * Get the grid of squares board consists of.
     *
     * @return Square[]
     */
    public function grid(): array;
}
