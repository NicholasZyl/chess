<?php

namespace NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\PieceWasPlaced;

interface InitialPositions
{
    /**
     * Initialise board with pieces at predefined positions.
     *
     * @param Board $board
     *
     * @return PieceWasPlaced[]
     */
    public function initialiseBoard(Board $board): array;
}
