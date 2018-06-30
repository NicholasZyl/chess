<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Fide\Rules\BishopMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\KingCheck;
use NicholasZyl\Chess\Domain\Fide\Rules\KingMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\KnightMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\QueenMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\RookMoves;
use NicholasZyl\Chess\Domain\Rules;

final class LawsOfChess
{
    /**
     * Prepare set of rules according to Fide handbook.
     *
     * @return Rules
     */
    public function rules(): Rules
    {
        return new Rules(
            [
                new PawnMoves(),
                new KnightMoves(),
                new BishopMoves(),
                new RookMoves(),
                new QueenMoves(),
                new KingMoves(),
                new KingCheck(),
            ]
        );
    }
}
