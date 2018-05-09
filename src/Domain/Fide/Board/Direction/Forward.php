<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Piece\Color;

final class Forward implements Direction
{
    /**
     * @var Color
     */
    private $color;

    /**
     * @var Direction
     */
    private $direction;

    /**
     * Forward constructor.
     * @param Color $color
     * @param Direction $direction
     */
    public function __construct(Color $color, Direction $direction)
    {
        $this->color = $color;
        $this->direction = $direction;
    }
    /**
     * {@inheritdoc}
     */
    public function areOnSame(Coordinates $from, Coordinates $to): bool
    {
        return $this->direction->areOnSame($from, $to);
    }

    /**
     * {@inheritdoc}
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        $isForward = $this->color->is(Color::white()) ? $from->rank() < $to->rank() : $to->rank() < $from->rank();
        if (!$isForward) {
            throw new InvalidDirection($from, $to, $this);
        }

        return $this->direction->nextCoordinatesTowards($from, $to);
    }

    /**
     * {@inheritdoc}
     */
    public function inSameDirectionAs(Direction $direction): bool
    {
        return $direction instanceof self && $this->color->is($direction->color) && $this->direction->inSameDirectionAs($direction->direction);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('forward %s', $this->direction);
    }
}
