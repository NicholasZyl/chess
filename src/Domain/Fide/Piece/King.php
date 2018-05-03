<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class King extends Piece
{
    private const ALLOWED_MOVE_DISTANCE = 1;

    /**
     * {@inheritdoc}
     */
    public function intentMove(CoordinatePair $from, CoordinatePair $to): Move
    {
        $move = Move::between($from, $to);
        if ((!$move->isAlongDiagonal() && !$move->isAlongRank() && !$move->isAlongFile()) || $move->isAwayMoreSquaresThan(self::ALLOWED_MOVE_DISTANCE)) {
            throw new IllegalMove($from, $to);
        }

        return $move;
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(\NicholasZyl\Chess\Domain\Move $move): void
    {
        if ($move instanceof Move\NearestNotSameFileRankOrDiagonal || count($move) > self::ALLOWED_MOVE_DISTANCE) {
            throw IllegalMove::forMove($move);
        }
    }
}
