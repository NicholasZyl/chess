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
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        $this->validateNotOnSameFileOrRankOrDiagonal($from, $to);

        $this->validateIsTheNearest($from, $to);

        return $to;
    }

    /**
     * Validate both points are not along the same file, rank or diagonal.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @throws InvalidDirection
     *
     * @return void
     */
    private function validateNotOnSameFileOrRankOrDiagonal(Coordinates $from, Coordinates $to): void
    {
        $sameFile = $from->file() === $to->file();
        $sameRank = $from->rank() === $to->rank();
        $sameDiagonal = abs($from->rank() - $to->rank()) === abs(ord($from->file()) - ord($to->file()));
        if ($sameFile || $sameRank || $sameDiagonal) {
            throw new InvalidDirection($from, $to, $this);
        }
    }

    /**
     * Validate two points are the nearest ones to each other.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @throws InvalidDirection
     *
     * @return void
     */
    private function validateIsTheNearest(Coordinates $from, Coordinates $to): void
    {
        $distanceAlongFile = abs(ord($from->file()) - ord($to->file()));
        $distanceAlongRank = abs($from->rank() - $to->rank());

        if ($distanceAlongFile > self::DISTANCE_TO_THE_NEAREST_COORDINATES || $distanceAlongRank > self::DISTANCE_TO_THE_NEAREST_COORDINATES) {
            throw new InvalidDirection($from, $to, $this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'nearest but not along same file, rank or diagonal';
    }
}
