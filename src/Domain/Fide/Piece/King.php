<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Board\UnknownDirection;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Move;

final class King extends Piece
{
    private const MOVE_TO_ADJOINING_SQUARE = 1;

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
        if (!($move instanceof NotIntervened && $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE)) && (!$move instanceof Castling || $this->hasMoved) || $move->inDirection(new LShaped())) {
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
            $alongRank = new AlongRank();
            if ($alongRank->areOnSame($this->position, $destination) && $this->position->distanceTo($destination, $alongRank) === 2) {
                return new Castling(
                    $this->color(),
                    $this->position,
                    $destination
                );
            }

            return new NotIntervened(
                $this->position,
                $destination,
                $this->position->directionTo($destination)
            );
        } catch (InvalidDirection | UnknownDirection $exception) {
            throw new MoveToIllegalPosition($this, $this->position, $destination);
        }
    }
}
