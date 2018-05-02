<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\MovementRules;
use NicholasZyl\Chess\Domain\Piece\Rank;

final class RookMovementRules implements MovementRules
{
    /**
     * {@inheritdoc}
     */
    public function forRank(): Rank
    {
        return Rank::rook();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Color $color, Coordinates $from, Coordinates $to): void
    {
        $distance = $from->distance($to);
        if (!$distance->isVertical() && !$distance->isHorizontal()) {
            throw new IllegalMove($from, $to);
        }
    }
}
