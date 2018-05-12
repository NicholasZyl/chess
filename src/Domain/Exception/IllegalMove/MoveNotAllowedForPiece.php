<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalMove;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece;

final class MoveNotAllowedForPiece extends IllegalMove
{
    /**
     * Create exception for move that is illegal for given piece.
     *
     * @param Piece $piece
     * @param Move $move
     */
    public function __construct(Piece $piece, Move $move)
    {
        parent::__construct(
            sprintf('%s from %s to %s is not allowed for %s %s.', $move, $move->source(), $move->destination(), $piece->color(), $piece)
        );
    }
}