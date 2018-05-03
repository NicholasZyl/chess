<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Piece\Color;

interface Move extends \Iterator, \Countable
{
    /**
     * Is move made towards color's opponent's side of the board.
     *
     * @param Color $color
     *
     * @return bool
     */
    public function isTowardsOpponentSideFor(Color $color): bool;
}