<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\UnknownDirection;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Move;

final class Pawn extends Piece
{
    private const MOVE_TO_ADJOINING_SQUARE = 1;
    private const MOVE_ADVANCING_TWO_SQUARES = 2;

    /**
     * @var bool
     */
    private $hasMoved = false;

    /**
     * @var Board\Coordinates
     */
    private $position;

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'pawn';
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(Move $move, Board $board): void
    {
        if ($this->isLegalMove($move)) {
            try {
                $board->verifyThatPositionIsUnoccupied($move->destination());
            } catch (SquareIsOccupied $squareIsOccupied) {
                throw new MoveNotAllowedForPiece($this, $move);
            }
            return;
        }

        if ($this->isLegalCapture($move, $board)) {
            return;
        }

        throw new MoveNotAllowedForPiece($this, $move);
    }

    /**
     * Is move a legal one for pawn.
     *
     * @param Move $move
     *
     * @return bool
     */
    private function isLegalMove(Move $move): bool
    {
        return $move->inDirection(new Forward($this->color(), new AlongFile()))
            && (
                !$this->hasMoved && $move->isOverDistanceOf(self::MOVE_ADVANCING_TWO_SQUARES)
                || $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE)
            );
    }

    /**
     * Is move a legal capture for pawn.
     *
     * @param Move $move
     * @param Board $board
     *
     * @return bool
     */
    private function isLegalCapture(Move $move, Board $board): bool
    {
        return $move->inDirection(new Forward($this->color(), new AlongDiagonal()))
            && $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE)
            && $board->hasOpponentsPieceAt($move->destination(), $this->color());
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
            if ($direction instanceof AlongDiagonal || $direction instanceof AlongFile) {
                return new NotIntervened(
                    $this->position,
                    $destination,
                    new Forward($this->color(), $direction)
                );
            }
        } catch (InvalidDirection | UnknownDirection $exception) {
            throw new MoveToIllegalPosition($this, $this->position, $destination);
        }

        throw new MoveToIllegalPosition($this, $this->position, $destination);
    }

    /**
     * {@inheritdoc}
     */
    public function isAttacking(Coordinates $coordinates, Board $board): bool
    {
        if (!$this->position->directionTo($coordinates) instanceof AlongDiagonal) {
            return false;
        }
        return parent::isAttacking($coordinates, $board);
    }
}
