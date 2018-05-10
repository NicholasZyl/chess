<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Move;

final class Bishop extends Piece
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

    /**
     * {@inheritdoc}
     */
    public function canMove(BoardMove $move): void
    {
        if (!$move->is(NotIntervened::class) || !$move->inDirection(new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal())) {
            throw new NotAllowedForPiece($this, $move);
        }
    }

    /**
     * Place piece at given coordinates.
     *
     * @param Board\Coordinates $coordinates
     *
     * @return void
     */
    public function placeAt(Board\Coordinates $coordinates): void
    {
        $this->position = $coordinates;
    }

    /**
     * Intent move from piece's current position to the destination.
     *
     * @param Board\Coordinates $destination
     *
     * @throws ToIllegalPosition
     *
     * @return BoardMove
     */
    public function intentMoveTo(Board\Coordinates $destination): BoardMove
    {
        try {
            return new NotIntervened(
                $this->position,
                $destination,
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
            );
        } catch (InvalidDirection $invalidDirection) {
            throw new ToIllegalPosition($this, $this->position, $destination);
        }
    }
}
