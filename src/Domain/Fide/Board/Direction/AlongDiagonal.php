<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;

final class AlongDiagonal implements Direction
{
    /**
     * {@inheritdoc}
     */
    public function areOnSame(Coordinates $from, Coordinates $to): bool
    {
        return abs($from->rank() - $to->rank()) === abs(ord($from->file()) - ord($to->file()));
    }

    /**
     * {@inheritdoc}
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        if (!$this->areOnSame($from, $to)) {
            throw new CoordinatesNotReachable($from, $to, $this);
        }

        $isTowardsKingside = ord($from->file()) < ord($to->file());
        $isTowardsHigherRank = $from->rank() < $to->rank();

        return CoordinatePair::fromFileAndRank(
            chr(ord($from->file()) + ($isTowardsKingside ? 1 : -1)),
            $from->rank() + ($isTowardsHigherRank ? 1 : -1)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function distanceBetween(Coordinates $from, Coordinates $to): int
    {
        if (!$this->areOnSame($from, $to)) {
            throw new CoordinatesNotReachable($from, $to, $this);
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
        return 'along same diagonal';
    }
}
