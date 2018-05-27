<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;

interface Move
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
     * Check if move is made over expected distance.
     *
     * @param int $expectedDistance
     *
     * @return bool
     */
    public function isOverDistanceOf(int $expectedDistance): bool;

    /**
     * Check if move is made in same direction.
     *
     * @param Direction $direction
     *
     * @return bool
     */
    public function inDirection(Direction $direction): bool;

    /**
     * Validate if move is legal.
     *
     * @param Board $board
     *
     * @throws IllegalMove
     *
     * @return void
     */
    public function isLegal(Board $board): void;

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
     * Get string representation of the move.
     *
     * @return string
     */
    public function __toString(): string;
}