<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class Knight extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function intentMove(CoordinatePair $from, CoordinatePair $to): Move
    {
        $move = Move::between($from, $to);
        if ($move->isAlongFile() || $move->isAlongRank() || $move->isAlongDiagonal() || $move->isAwayMoreSquaresThan(2)) {
            throw new IllegalMove($from, $to);
        }

        return $move;
    }
}
