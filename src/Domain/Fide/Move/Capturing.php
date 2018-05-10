<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\MoveToUnoccupiedPosition;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;

final class Capturing implements Move
{
    /**
     * @var Color
     */
    private $color;

    /**
     * @var Move
     */
    private $move;

    /**
     * Create move that ends in capture of opponent's piece.
     *
     * @param Color $color
     * @param Move $move
     */
    public function __construct(Color $color, Move $move)
    {
        $this->color = $color;
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
    public function __toString(): string
    {
        return sprintf('capturing after %s', $this->move);
    }

    /**
     * {@inheritdoc}
     */
    public function play(Board $board): void
    {
        if (!$board->hasOpponentsPieceAt($this->move->destination(), $this->color)) {
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
