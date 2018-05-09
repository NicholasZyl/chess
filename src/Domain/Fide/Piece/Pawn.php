<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\AlongFile;
use NicholasZyl\Chess\Domain\Move;

final class Pawn extends Piece
{
    private const ALLOWED_STEPS_COUNT_FOR_FIRST_MOVE = 1;
    private const ALLOWED_STEPS_COUNT_FOR_NEXT_MOVES = 0;

    /**
     * @var int
     */
    private $maximalDistance = self::ALLOWED_STEPS_COUNT_FOR_FIRST_MOVE;

    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        $hasOpponentsPieceAtDestination = $board->hasOpponentsPieceAt($move->to(), $this->color());
        $moveDistance = count($move->steps());
        $isForward = $move->isTowardsOpponentSideFor($this->color());

        if ($move instanceof AlongDiagonal && $hasOpponentsPieceAtDestination && $isForward && $moveDistance === self::ALLOWED_STEPS_COUNT_FOR_NEXT_MOVES) {
            return;
        }

        if (!$move instanceof AlongFile || !$isForward || $moveDistance > $this->maximalDistance) {
            throw new MoveNotAllowedForPiece($move, $this);
        }

        if ($hasOpponentsPieceAtDestination) {
            throw new MoveToOccupiedPosition($move->to());
        }
        $this->checkForInterveningPieces($move, $board);

        $this->maximalDistance = self::ALLOWED_STEPS_COUNT_FOR_NEXT_MOVES;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'pawn';
    }

    /**
     * Check if intended move is legal for this piece.
     * TODO: rename to mayMove
     *
     * @param BoardMove $move
     *
     * @throws IllegalMove
     *
     * @return void
     */
    public function canMove(BoardMove $move): void
    {
        if (!$move->direction() instanceof Forward) {
            throw new NotAllowedForPiece($this, $move);
        }
    }
}
