<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;

final class Knight extends Piece
{
    public function intentMove(Coordinates $from, Coordinates $to)
    {
        $move = Move::between($from, $to);
        if ($move->isVertical() || $move->isHorizontal() || $move->isDiagonal() || $move->isHigherThan(2)) {
            throw new IllegalMove($from, $to);
        }

        return $move;
    }
}
