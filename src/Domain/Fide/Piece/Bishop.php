<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;

final class Bishop extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function mayMove(\NicholasZyl\Chess\Domain\Move $move): void
    {
        if (!$move instanceof Move\AlongDiagonal) {
            throw IllegalMove::forMove($move);
        }
    }
}
