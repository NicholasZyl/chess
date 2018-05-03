<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\ChessboardMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class MoveAlongFile extends ChessboardMove
{
    /**
     * {@inheritdoc}
     */
    protected function validateIfMoveIsPossible(CoordinatePair $from, CoordinatePair $to): void
    {
        if (!$from->isOnSameFile($to)) {
            throw new \InvalidArgumentException(
                sprintf('%s and %s are not along the same file.', $from, $to)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function nextCoordinatesTowards(CoordinatePair $from, CoordinatePair $to): CoordinatePair
    {
        $isTowardsHigherRank = $from->rank() < $to->rank();

        return CoordinatePair::fromFileAndRank(
            $from->file(),
            $from->rank() + ($isTowardsHigherRank ? self::INCREMENT : self::DECREMENT)
        );
    }
}
