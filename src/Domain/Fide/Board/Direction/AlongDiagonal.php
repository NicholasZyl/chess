<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction\Direction;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;

final class AlongDiagonal implements Direction
{
    /**
     * {@inheritdoc}
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        if (abs($from->rank() - $to->rank()) !== abs(ord($from->file()) - ord($to->file()))) {
            throw new InvalidDirection($from, $to, $this);
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
    public function __toString(): string
    {
        return 'along same diagonal';
    }
}
