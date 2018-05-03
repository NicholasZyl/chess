<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;

final class King extends Piece
{
    private const ALLOWED_MOVE_DISTANCE = 1;

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
