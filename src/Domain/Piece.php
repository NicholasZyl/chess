<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
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
     * Intent piece's move from one square to another, validating if it abide the rules.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     *
     * @return Move
     */
    public function intentMove(CoordinatePair $from, CoordinatePair $to): Move;

    /**
     * Check if intented move is legal for this piece.
     *
     * @param \NicholasZyl\Chess\Domain\Move $move
     *
     * @throws IllegalMove
     *
     * @return void
     */
    public function mayMove(\NicholasZyl\Chess\Domain\Move $move): void;
}
