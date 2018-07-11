<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

interface GameArrangement
{
    /**
     * Get the set of rules that should be used during the game.
     *
     * @return Rules
     */
    public function rules(): Rules;

    /**
     * Initialise board with pieces at predefined positions.
     *
     * @param Board $board
     *
     * @return void
     */
    public function initialiseBoard(Board $board): void;
}
