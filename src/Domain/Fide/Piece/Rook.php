<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Exception\Board\UnknownDirection;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Move;

final class Rook extends Piece
{
    /**
     * @var Board\Coordinates
     */
    private $position;

    /**
     * @var bool
     */
    private $hasMoved;

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
    public function mayMove(Move $move, Board $board): void
    {
        if (!$move instanceof NotIntervened && (!$move instanceof Castling || $this->hasMoved) || !($move->inDirection(new AlongFile()) || $move->inDirection(new AlongRank()))) {
            throw new MoveNotAllowedForPiece($this, $move);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function placeAt(Board\Coordinates $coordinates): void
    {
        $this->hasMoved = !is_null($this->position);
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
                throw new MoveToIllegalPosition($this, $this->position, $destination);
            }

            return new NotIntervened(
                $this->position,
                $destination,
                $direction
            );
        } catch (CoordinatesNotReachable | UnknownDirection $exception) {
            throw new MoveToIllegalPosition($this, $this->position, $destination);
        }
    }
}
