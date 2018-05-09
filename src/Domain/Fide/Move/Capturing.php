<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\MoveToUnoccupiedPosition;

final class Capturing implements BoardMove
{
    /**
     * @var BoardMove
     */
    private $move;

    public function __construct(BoardMove $move)
    {
        $this->move = $move;
    }

    /**
     * Get the source coordinates.
     *
     * @return Coordinates
     */
    public function source(): Coordinates
    {
        return $this->move->source();
    }

    /**
     * Get the destination coordinates.
     *
     * @return Coordinates
     */
    public function destination(): Coordinates
    {
        return $this->move->destination();
    }

    /**
     * Get the move direction.
     *
     * @return Board\Direction
     */
    public function direction(): Board\Direction
    {
        return $this->move->direction();
    }

    /**
     * Get string representation of the move.
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('capturing after %s', $this->move);
    }

    /**
     * Play the move on the board.
     *
     * @param Board $board
     *
     * @throws IllegalMove
     *
     * @return void
     */
    public function play(Board $board): void
    {
        $piece = $board->peekPieceAtCoordinates($this->move->source());
        if (!$board->hasOpponentsPieceAt($this->move->destination(), $piece->color())) {
            throw new MoveToUnoccupiedPosition($this->move->destination());
        }
        $this->move->play($board);
    }
}
