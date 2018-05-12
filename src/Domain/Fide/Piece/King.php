<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Board\UnknownDirection;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\Move\TooDistant;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Move;

final class King extends Piece
{
    /**
     * @var Board\Coordinates
     */
    private $position;

    /**
     * @var bool
     */
    private $hasMoved = false;

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
    public function mayMove(Move $move, Board $board): void
    {
        if (!$move instanceof ToAdjoiningSquare && (!$move instanceof Castling || $this->hasMoved) || $move->inDirection(new LShaped())) {
            throw new NotAllowedForPiece($this, $move);
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
            $alongRank = new AlongRank();
            if (!$this->hasMoved && $alongRank->areOnSame($this->position, $destination) && $this->position->distanceTo($destination, $alongRank) === 2) {
                return new Castling(
                    $this->color(),
                    $this->position,
                    $destination
                );
            }

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
