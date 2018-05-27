<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
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
            return new OverOtherPieces(
                $this->position,
                $destination,
                new LShaped()
            );
        } catch (CoordinatesNotReachable $exception) {
            throw new MoveToIllegalPosition($this, $this->position, $destination);
        }
    }
}
