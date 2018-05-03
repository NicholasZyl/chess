<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\ChessboardMove;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class Rook extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function intentMove(CoordinatePair $from, CoordinatePair $to): Move
    {
        $move = Move::between($from, $to);
        if (!$move->isAlongFile() && !$move->isAlongRank()) {
            throw new IllegalMove($from, $to);
        }

        return $move;
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(ChessboardMove $move): void
    {
        if (!$move instanceof Move\AlongFile && !$move instanceof Move\AlongRank) {
            throw IllegalMove::forMove($move);
        }
    }
}
