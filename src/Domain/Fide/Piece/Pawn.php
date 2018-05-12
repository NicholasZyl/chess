<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\UnknownDirection;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveTooDistant;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Move\AdvancingTwoSquares;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Move;

final class Pawn extends Piece
{
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
        if ($move instanceof AdvancingTwoSquares && !$this->hasMoved && $move->inDirection(new Forward($this->color(), new AlongFile()))) {
            try {
                $board->verifyThatPositionIsUnoccupied($move->destination());
            } catch (SquareIsOccupied $squareIsOccupied) {
                throw new MoveNotAllowedForPiece($this, $move);
            }
            return;
        }

        if ($move instanceof ToAdjoiningSquare) {
            if ($move->inDirection(new Forward($this->color(), new AlongDiagonal())) && $board->hasOpponentsPieceAt($move->destination(), $this->color())) {
                return;
            }
            if ($move->inDirection(new Forward($this->color(), new AlongFile()))) {
                try {
                    $board->verifyThatPositionIsUnoccupied($move->destination());
                } catch (SquareIsOccupied $squareIsOccupied) {
                    throw new MoveNotAllowedForPiece($this, $move);
                }
                return;
            }
        }

        throw new MoveNotAllowedForPiece($this, $move);
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
            if ($direction instanceof AlongDiagonal) {
                return new ToAdjoiningSquare(
                    $this->position,
                    $destination,
                    new Forward($this->color(), $direction)
                );
            }

            if ($direction instanceof AlongFile) {
                return $this->intentMoveToUnoccupiedSquare($destination, $direction);
            }
        } catch (InvalidDirection | UnknownDirection | MoveTooDistant $exception) {
            throw new MoveToIllegalPosition($this, $this->position, $destination);
        }

        throw new MoveToIllegalPosition($this, $this->position, $destination);
    }

    /**
     * Prepare move to unoccupied square basing on information if pawn already moved or not.
     *
     * @param Board\Coordinates $destination
     * @param Board\Direction $direction
     *
     * @throws InvalidDirection
     * @throws MoveTooDistant
     *
     * @return Move
     */
    private function intentMoveToUnoccupiedSquare(Board\Coordinates $destination, Board\Direction $direction): Move
    {
        if ($this->hasMoved) {
            $move = new ToAdjoiningSquare(
                $this->position,
                $destination,
                new Forward($this->color(), $direction)
            );
        } else {
            $move = new AdvancingTwoSquares(
                $this->position,
                $destination,
                new Forward($this->color(), $direction)
            );
        }

        return $move;
    }
}
