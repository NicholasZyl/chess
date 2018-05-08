<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction\Direction;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;

final class AlongFile implements Direction
{
    /**
     * {@inheritdoc}
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        if ($from->file() !== $to->file()) {
            throw new InvalidDirection($from, $to, $this);
        }

        return CoordinatePair::fromFileAndRank($from->file(), $from->rank() + ($from->rank() < $to->rank() ? 1 : -1));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'along same file';
    }
}
