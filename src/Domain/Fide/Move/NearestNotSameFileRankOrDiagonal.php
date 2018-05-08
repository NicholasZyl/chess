<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\ChessboardMove;

final class NearestNotSameFileRankOrDiagonal extends ChessboardMove
{
    private const DISTANCE_TO_NEAREST_COORDINATES = 2;

    /**
     * {@inheritdoc}
     */
    protected function validateIfMoveIsPossible(CoordinatePair $from, CoordinatePair $to): void
    {
        if ($from->isOnSameFile($to) || $from->isOnSameRank($to) || $from->isOnSameDiagonal($to)) {
            throw new \InvalidArgumentException(
                sprintf('%s and %s are along the same file, rank or diagonal.', $from, $to)
            );
        }

        $distanceAlongFile = abs(ord($from->file()) - ord($to->file()));
        $distanceAlongRank = abs($from->rank() - $to->rank());

        if ($distanceAlongFile > self::DISTANCE_TO_NEAREST_COORDINATES || $distanceAlongRank > self::DISTANCE_TO_NEAREST_COORDINATES) {
            throw new \InvalidArgumentException(
                sprintf('%s and %s are not the nearest squares.', $from, $to)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function nextCoordinatesTowards(CoordinatePair $from, CoordinatePair $to): CoordinatePair
    {
        return $to;
    }
}
