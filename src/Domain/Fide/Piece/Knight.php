<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Move;

final class Knight extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        if (!$move instanceof NearestNotSameFileRankOrDiagonal) {
            throw new MoveNotAllowedForPiece($move, $this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'knight';
    }

    /**
     * Validate if given move is legal for this piece.
     *
     * @param BoardMove $move
     *
     * @return void
     */
    public function canMove(BoardMove $move): void
    {
        if (!$move->is(OverOtherPieces::class) || !$move->inDirection(new LShaped())) {
            throw new NotAllowedForPiece($this, $move);
        }
    }
}
