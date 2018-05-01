<?php

namespace NicholasZyl\Chess\Domain\Chessboard;

interface Rules
{

    /**
     * Validate if proposed move is valid according to the game's rules.
     *
     * @param Square $from
     * @param Square $to
     *
     * @return void
     */
    public function validateMove(Square $from, Square $to): void;
}
