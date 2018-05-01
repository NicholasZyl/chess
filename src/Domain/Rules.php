<?php

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\Rules\Exception\MissingRule;

interface Rules
{

    /**
     * Validate if proposed move is valid according to the game's rules.
     *
     * @param Square $from
     * @param Square $to
     *
     * @throws MissingRule
     * @throws IllegalMove
     *
     * @return void
     */
    public function validateMove(Square $from, Square $to): void;
}
