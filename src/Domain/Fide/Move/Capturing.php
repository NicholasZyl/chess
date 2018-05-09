<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\MoveToUnoccupiedPosition;

final class Capturing implements BoardMove
{
    /**
     * @var BoardMove
     */
    private $move;

    /**
     * Create move that ends in capture of opponent's piece.
     *
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
    public function distance(): int
    {
        return $this->move->distance();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('capturing after %s', $this->move);
    }

    /**
     * {@inheritdoc}
     */
    public function play(Board $board): void
    {
        $piece = $board->peekPieceAtCoordinates($this->move->source());
        if (!$board->hasOpponentsPieceAt($this->move->destination(), $piece->color())) {
            throw new MoveToUnoccupiedPosition($this->move->destination());
        }
        $this->move->play($board);
    }

    /**
     * {@inheritdoc}
     */
    public function is(string $moveType): bool
    {
        return $this instanceof $moveType || $this->move->is($moveType);
    }

    /**
     * {@inheritdoc}
     */
    public function inDirection(Board\Direction $direction): bool
    {
        return $this->move->inDirection($direction);
    }
}
