<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Move;

final class Bishop extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        if (!$move instanceof AlongDiagonal) {
            throw IllegalMove::forMove($move);
        }

        $this->checkForInterveningPieces($move, $board);
    }
}
