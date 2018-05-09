<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Move;

final class Bishop extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        if (!$move instanceof AlongDiagonal) {
            throw new MoveNotAllowedForPiece($move, $this);
        }

        $this->checkForInterveningPieces($move, $board);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'bishop';
    }

    public function canMove($argument1)
    {
        // TODO: write logic here
    }
}
