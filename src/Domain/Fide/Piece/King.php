<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;

final class King extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function intentMove(Coordinates $from, Coordinates $to): Move
    {
        $move = Move::between($from, $to);
        if ($move->isHigherThan(1)) {
            throw new IllegalMove($from, $to);
        }

        return $move;
    }
}
