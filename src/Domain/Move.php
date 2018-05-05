<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Position\Coordinates;
use NicholasZyl\Chess\Domain\Piece\Color;

interface Move extends \Iterator, \Countable
{

    /**
     * Get move's starting coordinates.
     *
     * @return Coordinates
     */
    public function from(): Coordinates;

    /**
     * Get move's destination coordinates.
     *
     * @return Coordinates
     */
    public function to(): Coordinates;

    /**
     * Is move made towards color's opponent's side of the board.
     *
     * @param Color $color
     *
     * @return bool
     */
    public function isTowardsOpponentSideFor(Color $color): bool;
}