<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;

final class ToUnoccupiedSquare implements BoardMove
{
    /**
     * @var BoardMove
     */
    private $move;

    /**
     * Create move to an unoccupied square.
     * @param BoardMove $move
     */
    public function __construct(BoardMove $move)
    {
        $this->move = $move;
    }

    /**
     * {@inheritdoc}
     */
    public function source(): Coordinates
    {
        return $this->move->source();
    }

    /**
     * {@inheritdoc}
     */
    public function destination(): Coordinates
    {
        return $this->move->destination();
    }

    /**
     * {@inheritdoc}
     */
    public function direction(): Board\Direction
    {
        return $this->move->direction();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('%s to unoccupied square', $this->move);
    }

    /**
     * {@inheritdoc}
     */
    public function play(Board $board): void
    {
        try {
            $board->verifyThatPositionIsUnoccupied($this->move->destination());
        } catch (SquareIsOccupied $squareIsOccupied) {
            throw new MoveToOccupiedPosition($squareIsOccupied->coordinates());
        }
        $this->move->play($board);
    }
}
