<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\MoveIsInvalid;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Move\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\NearestNotSameFileRankOrDiagonal;

final class MoveIntention
{
    /**
     * Intent move between two coordinates in a known movement type.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @throws IllegalMove
     *
     * @return ChessboardMove
     */
    public function intentMove(Coordinates $from, Coordinates $to): ChessboardMove
    {
        if (!$from instanceof CoordinatePair || !$to instanceof CoordinatePair) {
            throw new \InvalidArgumentException('Can intent move only for chessboard coordinates.');
        }

        try {
            if ($from->isOnSameFile($to)) {
                $move = AlongFile::between($from, $to);
            } elseif ($from->isOnSameRank($to)) {
                $move = AlongRank::between($from, $to);
            } elseif ($from->isOnSameDiagonal($to)) {
                $move = AlongDiagonal::between($from, $to);
            } else {
                $move = NearestNotSameFileRankOrDiagonal::between($from, $to);
            }
        } catch (\InvalidArgumentException $invalidArgumentException) {
            throw new MoveIsInvalid($from, $to);
        }

        return $move;
    }
}
