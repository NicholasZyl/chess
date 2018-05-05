<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Chessboard\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Move;

final class King extends Piece
{
    private const ALLOWED_MOVE_DISTANCE = 1;

    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        if ($move instanceof NearestNotSameFileRankOrDiagonal || count($move) > self::ALLOWED_MOVE_DISTANCE) {
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
