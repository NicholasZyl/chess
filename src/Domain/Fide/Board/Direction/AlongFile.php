<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;

final class AlongFile implements Direction
{
    /**
     * {@inheritdoc}
     */
    public function areOnSame(Coordinates $from, Coordinates $to): bool
    {
        return $from->file() === $to->file();
    }

    /**
     * {@inheritdoc}
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        if (!$this->areOnSame($from, $to)) {
            throw new InvalidDirection($from, $to, $this);
        }

        return CoordinatePair::fromFileAndRank($from->file(), $from->rank() + ($from->rank() < $to->rank() ? 1 : -1));
    }

    /**
     * {@inheritdoc}
     */
    public function distanceBetween(Coordinates $from, Coordinates $to): int
    {
        if (!$this->areOnSame($from, $to)) {
            throw new InvalidDirection($from, $to, $this);
        }

        return abs($to->rank() - $from->rank());
    }

    /**
     * {@inheritdoc}
     */
    public function inSameDirectionAs(Direction $direction): bool
    {
        return $direction instanceof self;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'along same file';
    }
}
