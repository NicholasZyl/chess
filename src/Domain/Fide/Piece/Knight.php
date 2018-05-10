<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Move;

final class Knight extends Piece
{
    /**
     * @var Board\Coordinates
     */
    private $position;

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
     * {@inheritdoc}
     */
    public function canMove(BoardMove $move): void
    {
        if (!$move->is(OverOtherPieces::class) || !$move->inDirection(new LShaped())) {
            throw new NotAllowedForPiece($this, $move);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function placeAt(Board\Coordinates $coordinates): void
    {
        $this->position = $coordinates;
    }

    /**
     * {@inheritdoc}
     */
    public function intentMoveTo(Board\Coordinates $destination): BoardMove
    {
        try {
            return new OverOtherPieces(
                $this->position,
                $destination,
                new LShaped()
            );
        } catch (InvalidDirection $exception) {
            throw new ToIllegalPosition($this, $this->position, $destination);
        }
    }
}
