<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
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

    /**
     * Check if move is done same way as provided.
     *
     * @param string $moveType
     *
     * @return bool
     */
    public function is(string $moveType): bool;

    /**
     * Check if move is made in same direction.
     *
     * @param Direction $direction
     *
     * @return bool
     */
    public function inDirection(Direction $direction): bool;
}