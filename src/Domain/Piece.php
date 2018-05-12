<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
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
     * Compare if piece has the same color as another one.
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
     * Represent piece as a string.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Validate if given move is legal for this piece.
     *
     * @param Move $move
     * @param Board $board
     *
     * @throws MoveNotAllowedForPiece
     *
     * @return void
     */
    public function mayMove(Move $move, Board $board): void;

    /**
     * Place piece at given coordinates.
     *
     * @param Board\Coordinates $coordinates
     *
     * @return void
     */
    public function placeAt(Board\Coordinates $coordinates): void;

    /**
     * Intent move from piece's current position to the destination.
     *
     * @param Board\Coordinates $destination
     *
     * @throws MoveToIllegalPosition
     *
     * @return Move
     */
    public function intentMoveTo(Board\Coordinates $destination): Move;
}
