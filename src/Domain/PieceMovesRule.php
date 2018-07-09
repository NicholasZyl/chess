<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;

interface PieceMovesRule extends Rule
{
    /**
     * Get piece rank the rule is applicable for.
     *
     * @return string
     */
    public function isFor(): string;

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