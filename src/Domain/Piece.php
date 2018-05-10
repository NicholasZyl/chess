<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
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
     * @param BoardMove $move
     *
     * @throws NotAllowedForPiece
     *
     * @return void
     */
    public function canMove(BoardMove $move): void;

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
     * @throws ToIllegalPosition
     *
     * @return BoardMove
     */
    public function intentMoveTo(Board\Coordinates $destination): BoardMove;
}
