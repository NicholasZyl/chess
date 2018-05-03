<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\ChessboardMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class NearestSquareNotFileRankOrDiagonal extends ChessboardMove
{
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

        if ($distanceAlongFile > 2 || $distanceAlongRank > 2) {
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
