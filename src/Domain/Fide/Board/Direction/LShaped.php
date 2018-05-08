<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;

final class LShaped implements Direction
{
    private const DISTANCE_TO_THE_NEAREST_COORDINATES = 2;

    /**
     * {@inheritdoc}
     */
    public function areOnSame(Coordinates $from, Coordinates $to): bool
    {
        return $this->areNotOnSameFileOrRankOrDiagonal($from, $to) && $this->areTheNearest($from, $to);
    }

    /**
     * Check if two points are not along the same file, rank or diagonal.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @return bool
     */
    private function areNotOnSameFileOrRankOrDiagonal(Coordinates $from, Coordinates $to): bool
    {
        $sameFile = $from->file() === $to->file();
        $sameRank = $from->rank() === $to->rank();
        $sameDiagonal = abs($from->rank() - $to->rank()) === abs(ord($from->file()) - ord($to->file()));

        return !$sameFile && !$sameRank && !$sameDiagonal;
    }

    /**
     * Check if two points are the nearest ones to each other.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @return bool
     */
    private function areTheNearest(Coordinates $from, Coordinates $to): bool
    {
        $distanceAlongFile = abs(ord($from->file()) - ord($to->file()));
        $distanceAlongRank = abs($from->rank() - $to->rank());

        return $distanceAlongFile <= self::DISTANCE_TO_THE_NEAREST_COORDINATES && $distanceAlongRank <= self::DISTANCE_TO_THE_NEAREST_COORDINATES;
    }

    /**
     * {@inheritdoc}
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        if (!$this->areOnSame($from, $to)) {
            throw new InvalidDirection($from, $to, $this);
        }

        return $to;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'nearest but not along same file, rank or diagonal';
    }
}
