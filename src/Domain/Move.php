<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Piece\Color;

interface Move
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
     * Get coordinates of steps between starting and destination points.
     *
     * @return Coordinates[]
     */
    public function steps(): array;

    /**
     * Is move made towards color's opponent's side of the board.
     *
     * @param Color $color
     *
     * @return bool
     */
    public function isTowardsOpponentSideFor(Color $color): bool;
}