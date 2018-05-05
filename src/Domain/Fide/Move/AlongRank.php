<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Fide\ChessboardMove;
use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;

final class AlongRank extends ChessboardMove
{
    /**
     * {@inheritdoc}
     */
    protected function validateIfMoveIsPossible(CoordinatePair $from, CoordinatePair $to): void
    {
        if (!$from->isOnSameRank($to)) {
            throw new \InvalidArgumentException(
                sprintf('%s and %s are not along the same rank.', $from, $to)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function nextCoordinatesTowards(CoordinatePair $from, CoordinatePair $to): CoordinatePair
    {
        $isTowardsKingside = ord($from->file()) < ord($to->file());

        return CoordinatePair::fromFileAndRank(
            chr(ord($from->file()) + ($isTowardsKingside ? self::INCREMENT : self::DECREMENT)),
            $from->rank()
        );
    }
}
