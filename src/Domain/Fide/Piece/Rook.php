<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\UnknownDirection;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Move;

final class Rook extends Piece
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
        return 'rook';
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move): void
    {
        if (!$move->is(NotIntervened::class) || !($move->inDirection(new AlongFile()) || $move->inDirection(new AlongRank()))) {
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
            $direction = $this->position->directionTo($destination);
            if (!$direction instanceof AlongFile && !$direction instanceof AlongRank) {
                throw new ToIllegalPosition($this, $this->position, $destination);
            }

            return new NotIntervened(
                $this->position,
                $destination,
                $direction
            );
        } catch (InvalidDirection | UnknownDirection $exception) {
            throw new ToIllegalPosition($this, $this->position, $destination);
        }
    }
}
