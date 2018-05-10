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
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\UnknownDirection;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Move\AdvancingTwoSquares;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Move\Capturing;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Fide\Move\ToUnoccupiedSquare;
use NicholasZyl\Chess\Domain\Move;

final class Pawn extends Piece
{
    private const ALLOWED_STEPS_COUNT_FOR_FIRST_MOVE = 1;
    private const ALLOWED_STEPS_COUNT_FOR_NEXT_MOVES = 0;

    /**
     * @var int
     */
    private $maximalDistance = self::ALLOWED_STEPS_COUNT_FOR_FIRST_MOVE;

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
    public function mayMove(Move $move, Board $board): void
    {
        $hasOpponentsPieceAtDestination = $board->hasOpponentsPieceAt($move->to(), $this->color());
        $moveDistance = count($move->steps());
        $isForward = $move->isTowardsOpponentSideFor($this->color());

        if ($move instanceof AlongDiagonal && $hasOpponentsPieceAtDestination && $isForward && $moveDistance === self::ALLOWED_STEPS_COUNT_FOR_NEXT_MOVES) {
            return;
        }

        if (!$move instanceof AlongFile || !$isForward || $moveDistance > $this->maximalDistance) {
            throw new MoveNotAllowedForPiece($move, $this);
        }

        if ($hasOpponentsPieceAtDestination) {
            throw new MoveToOccupiedPosition($move->to());
        }
        $this->checkForInterveningPieces($move, $board);

        $this->maximalDistance = self::ALLOWED_STEPS_COUNT_FOR_NEXT_MOVES;
    }

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
    public function canMove(BoardMove $move): void
    {
        if ($move->is(Capturing::class)) {
            $this->validateCapturingMove($move);

            return;
        }

        if ($move->is(ToUnoccupiedSquare::class)) {
            $this->validateMoveToUnoccupiedSquare($move);

            return;
        }

        throw new NotAllowedForPiece($this, $move);
    }

    /**
     * Validate if capturing move is legal.
     *
     * @param BoardMove $move
     *
     * @return void
     */
    private function validateCapturingMove(BoardMove $move): void
    {
        if (!$move->is(ToAdjoiningSquare::class)) {
            throw new NotAllowedForPiece($this, $move);
        }

        if (!$move->inDirection(new Forward($this->color(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()))) {
            throw new NotAllowedForPiece($this, $move);
        }
    }

    /**
     * Validate if move to unoccupied square is legal.
     *
     * @param BoardMove $move
     *
     * @return void
     */
    private function validateMoveToUnoccupiedSquare(BoardMove $move): void
    {
        if (!$move->inDirection(new Forward($this->color(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()))) {
            throw new NotAllowedForPiece($this, $move);
        }

        if ($this->hasMoved && !$move->is(ToAdjoiningSquare::class)) {
            throw new NotAllowedForPiece($this, $move);
        }

        if (!$this->hasMoved && !$move->is(AdvancingTwoSquares::class) && !$move->is(ToAdjoiningSquare::class)) {
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
    public function intentMoveTo(Board\Coordinates $destination): BoardMove
    {
        try {
            $direction = $this->position->directionTo($destination);
            if ($direction instanceof \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal) {
                return new Capturing(
                    $this->color(),
                    new ToAdjoiningSquare(
                        $this->position,
                        $destination,
                        new Forward($this->color(), $direction)
                    )
                );
            }

            if ($direction instanceof \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile) {
                return $this->intentMoveToUnoccupiedSquare($destination, $direction);
            }
        } catch (InvalidDirection | UnknownDirection | TooDistant $exception) {
            throw new ToIllegalPosition($this, $this->position, $destination);
        }

        throw new ToIllegalPosition($this, $this->position, $destination);
    }

    /**
     * Prepare move to unoccupied square basing on information if pawn already moved or not.
     *
     * @param Board\Coordinates $destination
     * @param Board\Direction $direction
     *
     * @throws InvalidDirection
     * @throws TooDistant
     *
     * @return ToUnoccupiedSquare
     */
    private function intentMoveToUnoccupiedSquare(Board\Coordinates $destination, Board\Direction $direction): ToUnoccupiedSquare
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

        return new ToUnoccupiedSquare(
            $move
        );
    }
}
