<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
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
    public function __toString(): string
    {
        return 'bishop';
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        if (!$move instanceof NotIntervened || !$move->inDirection(new AlongDiagonal())) {
            throw new MoveNotAllowedForPiece($this, $move);
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
    public function intentMoveTo(Board\Coordinates $destination): Move
    {
        try {
            return new NotIntervened(
                $this->position,
                $destination,
                new AlongDiagonal()
            );
        } catch (InvalidDirection $invalidDirection) {
            throw new MoveToIllegalPosition($this, $this->position, $destination);
        }
    }
}
