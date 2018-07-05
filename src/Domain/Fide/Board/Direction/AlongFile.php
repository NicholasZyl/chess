<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;

final class AlongFile implements Direction
{
    /**
     * @var bool
     */
    private $towardsHigherRank;

    public function __construct(bool $towardsHigherRank = true)
    {
        $this->towardsHigherRank = $towardsHigherRank;
    }

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
        if ($this->distanceBetween($from, $to) === 0) {
            throw new CoordinatesNotReachable($from, $to, $this);
        }

        return CoordinatePair::fromFileAndRank($from->file(), $from->rank() + ($from->rank() < $to->rank() ? 1 : -1));
    }

    /**
     * {@inheritdoc}
     */
    public function distanceBetween(Coordinates $from, Coordinates $to): int
    {
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

    public function nextAlongFrom(Coordinates $position): Coordinates
    {
        return CoordinatePair::fromFileAndRank(
            $position->file(),
            $position->rank() + ($this->towardsHigherRank ? 1 : -1)
        );
    }
}
