<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;

//TODO: Rename to Move
interface BoardMove
{
    /**
     * Get the source coordinates.
     *
     * @return Coordinates
     */
    public function source(): Coordinates;

    /**
     * Get the destination coordinates.
     *
     * @return Coordinates
     */
    public function destination(): Coordinates;

    /**
     * Get the move direction.
     *
     * @return Board\Direction
     */
    public function direction(): Board\Direction;

    /**
     * Get string representation of the move.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Play the move on the board.
     *
     * @param Board $board
     *
     * @throws IllegalMove
     *
     * @return void
     */
    public function play(Board $board): void;
}