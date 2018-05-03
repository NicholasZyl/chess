<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\ChessboardMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class MoveAlongDiagonal extends ChessboardMove
{
    /**
     * {@inheritdoc}
     */
    protected function validateIfMoveIsPossible(CoordinatePair $from, CoordinatePair $to): void
    {
        if (!$from->isOnSameDiagonal($to)) {
            throw new \InvalidArgumentException(
                sprintf('%s and %s are not along the same diagonal.', $from, $to)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function nextCoordinatesTowards(CoordinatePair $from, CoordinatePair $to): CoordinatePair
    {
        $isTowardsKingside = ord($from->file()) < ord($to->file());
        $isTowardsHigherRank = $from->rank() < $to->rank();

        return CoordinatePair::fromFileAndRank(
            chr(ord($from->file()) + ($isTowardsKingside ? self::INCREMENT : self::DECREMENT)),
            $from->rank() + ($isTowardsHigherRank ? self::INCREMENT : self::DECREMENT)
        );
    }
}
