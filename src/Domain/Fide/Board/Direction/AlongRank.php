<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;

final class AlongRank implements Direction
{
    /**
     * {@inheritdoc}
     */
    public function areOnSame(Coordinates $from, Coordinates $to): bool
    {
        return $from->rank() === $to->rank();
    }

    /**
     * {@inheritdoc}
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        if ($this->distanceBetween($from, $to) === 0) {
            throw new CoordinatesNotReachable($from, $to, $this);
        }
        $isTowardsKingside = ord($from->file()) < ord($to->file());

        return CoordinatePair::fromFileAndRank(
            chr(ord($from->file()) + ($isTowardsKingside ? 1 : -1)),
            $from->rank()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function distanceBetween(Coordinates $from, Coordinates $to): int
    {
        return abs(ord($to->file()) - ord($from->file()));
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
        return 'along same rank';
    }
}
