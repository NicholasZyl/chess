<?php

namespace NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece\Rank;

interface RankMovementRules
{

    /**
     * For which rank given rule is.
     *
     * @return Rank
     */
    public function isFor(): Rank;

    /**
     * Check if proposed move from one coordinate to another is legal.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @throws IllegalMove
     * @return void
     */
    public function validate(Coordinates $from, Coordinates $to): void;
}
