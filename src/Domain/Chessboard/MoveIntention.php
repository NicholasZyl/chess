<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongFile;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongRank;
use NicholasZyl\Chess\Domain\Chessboard\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class MoveIntention
{
    /**
     * Intent move between two coordinates in a known movement type.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     *
     * @throws IllegalMove
     *
     * @return ChessboardMove
     */
    public function intentMove(CoordinatePair $from, CoordinatePair $to): ChessboardMove
    {
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
            throw new IllegalMove($from, $to);
        }

        return $move;
    }
}
