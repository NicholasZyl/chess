<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\Move\TooDistant;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\UnknownDirection;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Move;

final class King extends Piece
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

    /**
     * {@inheritdoc}
     */
    public function canMove(BoardMove $move): void
    {
        if (!$move->is(ToAdjoiningSquare::class) || $move->inDirection(new LShaped())) {
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
            return new ToAdjoiningSquare(
                $this->position,
                $destination,
                $this->position->directionTo($destination)
            );
        } catch (InvalidDirection | UnknownDirection | TooDistant $exception) {
            throw new ToIllegalPosition($this, $this->position, $destination);
        }
    }
}
