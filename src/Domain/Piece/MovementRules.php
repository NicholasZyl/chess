<?php

namespace NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;

interface MovementRules
{

    /**
     * For which rank given rule is.
     *
     * @return Rank
     */
    public function forRank(): Rank;

    /**
     * Check if proposed move from one coordinate to another is legal for given color.
     *
     * @param Color $color
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @return void
     */
    public function validate(Color $color, Coordinates $from, Coordinates $to): void;
}
