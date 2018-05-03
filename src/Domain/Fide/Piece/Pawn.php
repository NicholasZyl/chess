<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\ChessboardMove;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class Pawn extends Piece
{
    /**
     * @var bool
     */
    private $firstMove = true;

    private $maximalDistance = 2;

    /**
     * {@inheritdoc}
     */
    public function intentMove(CoordinatePair $from, CoordinatePair $to): Move
    {
        $move = Move::between($from, $to);
        if (!$move->isForward($this->color()) || $move->isAwayMoreSquaresThan($this->firstMove ? 2 : 1) || !$move->isAlongFile()) {
            throw new IllegalMove($from, $to);
        }
        $this->firstMove = false;

        return $move;
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(ChessboardMove $move): void
    {
        if (!$move instanceof Move\AlongFile || !$move->isTowardsOpponentSideFor($this->color()) || count($move) > $this->maximalDistance) {
            throw IllegalMove::forMove($move);
        }
        $this->maximalDistance = 1;
    }
}
