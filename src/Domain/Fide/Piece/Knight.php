<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
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
    public function __toString(): string
    {
        return 'knight';
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        if (!$move instanceof OverOtherPieces || !$move->inDirection(new LShaped())) {
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
    public function intentMoveTo(Board\Coordinates $destination): Move
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
