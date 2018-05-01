<?php

namespace NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;

interface Rules
{

    /**
     * Validate if proposed move is valid according to the game's rules.
     *
     * @param Square $from
     * @param Square $to
     *
     * @throws IllegalMove
     * @return void
     */
    public function validateMove(Square $from, Square $to): void;
}
