<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Fide\Rules\BishopMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\CastlingMove;
use NicholasZyl\Chess\Domain\Fide\Rules\KingMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\KnightMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\QueenMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\RookMoves;
use NicholasZyl\Chess\Domain\Rules\MoveRule;

final class LawsOfChess
{
    /**
     * Prepare set of rules according to Fide handbook.
     *
     * @return MoveRule[]
     */
    public function rules(): array
    {
        return
            [
                new PawnMoves(),
                new KnightMoves(),
                new BishopMoves(),
                new RookMoves(),
                new QueenMoves(),
                new KingMoves(),
                new CastlingMove(),
            ]
        ;
    }
}
