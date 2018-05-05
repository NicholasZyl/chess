<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece\Color;

interface Move extends \Iterator, \Countable
{

    /**
     * Get move's starting coordinates.
     *
     * @return CoordinatePair
     */
    public function from(): CoordinatePair;

    /**
     * Get move's destination coordinates.
     *
     * @return CoordinatePair
     */
    public function to(): CoordinatePair;

    /**
     * Is move made towards color's opponent's side of the board.
     *
     * @param Color $color
     *
     * @return bool
     */
    public function isTowardsOpponentSideFor(Color $color): bool;
}