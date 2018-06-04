<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;

final class Rules
{
    /**
     * @var PieceMoves[]
     */
    private $pieceMoves;

    /**
     * Create set of rules.
     *
     * @param array $pieceMoves
     */
    public function __construct(array $pieceMoves)
    {
        $this->pieceMoves = $pieceMoves;
    }

    /**
     * Check if move can be played for given piece.
     *
     * @param Piece $piece
     * @param Move $move
     *
     * @throws IllegalMove
     *
     * @return void
     */
    public function mayMove(Piece $piece, Move $move): void
    {
        foreach ($this->pieceMoves as $pieceMoveRules) {
            if ($pieceMoveRules->areApplicableFor($piece)) {
                $pieceMoveRules->mayMove($piece, $move);
            }
        }
    }
}
