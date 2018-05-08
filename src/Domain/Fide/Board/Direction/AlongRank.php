<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
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
        if (!$this->areOnSame($from, $to)) {
            throw new InvalidDirection($from, $to, $this);
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
    public function __toString(): string
    {
        return 'along same rank';
    }
}
