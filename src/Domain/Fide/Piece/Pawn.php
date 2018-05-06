<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
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
        if ($move instanceof AlongFile && $board->hasOpponentsPieceAt($move->to(), $this->color())) {
            throw new MoveToOccupiedPosition($move, $move->to());
        }

        if (!$move instanceof AlongFile || !$move->isTowardsOpponentSideFor($this->color()) || count($move->steps()) > $this->maximalDistance) {
            throw new MoveNotAllowedForPiece($move, $this);
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
}
