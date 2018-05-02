<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;

final class Pawn extends Piece
{
    private $firstMove = true;

    public function intentMove(Coordinates $from, Coordinates $to)
    {
        $move = Move::between($from, $to);
        if (!$move->isForward($this->color()) || $move->isHigherThan($this->firstMove ? 2 : 1) || !$move->isVertical()) {
            throw new IllegalMove($from, $to);
        }
        $this->firstMove = false;

        return $move;
    }
}
