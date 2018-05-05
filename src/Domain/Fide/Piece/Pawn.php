<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Chessboard;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongFile;
use NicholasZyl\Chess\Domain\Move;

final class Pawn extends Piece
{
    private const MAXIMAL_DISTANCE_FOR_FIRST_MOVE = 2;
    private const MAXIMAL_DISTANCE_FOR_NEXT_MOVES = 1;

    /**
     * @var int
     */
    private $maximalDistance = self::MAXIMAL_DISTANCE_FOR_FIRST_MOVE;

    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        if (!$move instanceof AlongFile || !$move->isTowardsOpponentSideFor($this->color()) || count($move) > $this->maximalDistance) {
            throw IllegalMove::forMove($move);
        }
        $this->checkForInterveningPieces($move, $board);

        $this->maximalDistance = self::MAXIMAL_DISTANCE_FOR_NEXT_MOVES;
    }
}
