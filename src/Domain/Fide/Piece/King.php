<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Move;

final class King extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        if ($move instanceof NearestNotSameFileRankOrDiagonal || !empty($move->steps())) {
            throw new MoveNotAllowedForPiece($move, $this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'king';
    }
}
