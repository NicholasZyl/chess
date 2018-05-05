<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece;

final class MoveNotAllowedForPiece extends IllegalMove
{
    /**
     * Create exception for move that's not allowed for specific piece.
     *
     * @param Move $move
     * @param Piece $piece
     */
    public function __construct(Move $move, Piece $piece)
    {
        parent::__construct(sprintf('Move between %s and %s is not allowed for %s.', $move->from(), $move->to(), $piece));
    }
}
