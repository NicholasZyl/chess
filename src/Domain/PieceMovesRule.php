<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;

interface PieceMovesRule extends Rule
{
    /**
     * Is rule applicable for given piece.
     *
     * @param Piece $piece
     *
     * @return bool
     */
    public function isApplicableFor(Piece $piece): bool;

    /**
     * Get all pseudo-legal destinations for a piece placed at given position.
     *
     * @param Piece $piece
     * @param Coordinates $actualPosition
     * @param Board $board
     *
     * @return \Generator
     */
    public function getLegalDestinationsFrom(Piece $piece, Coordinates $actualPosition, Board $board): \Generator;
}