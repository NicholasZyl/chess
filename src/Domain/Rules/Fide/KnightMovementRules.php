<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\MovementRules;
use NicholasZyl\Chess\Domain\Piece\Rank;

final class KnightMovementRules implements MovementRules
{
    /**
     * {@inheritdoc}
     */
    public function forRank(): Rank
    {
        return Rank::knight();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Color $color, Coordinates $from, Coordinates $to): void
    {
        $distance = Move::between($from, $to);
        if ($distance->isVertical() || $distance->isHorizontal() || $distance->isDiagonal() || $distance->isHigherThan(2)) {
            throw new IllegalMove($from, $to);
        }
    }
}
