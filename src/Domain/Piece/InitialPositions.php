<?php

namespace NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Board;

interface InitialPositions
{
    /**
     * Initialise board with pieces at predefined positions.
     *
     * @param Board $board
     *
     * @return void
     */
    public function initialiseBoard(Board $board): void;
}
