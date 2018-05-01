<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\MovementRules;

final class RookMovementRules implements MovementRules
{
    /**
     * {@inheritdoc}
     */
    public function isFor(): Rank
    {
        return Rank::rook();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Coordinates $from, Coordinates $to): void
    {
        $distance = $from->distance($to);
        if (!$distance->isVertical() && !$distance->isHorizontal()) {
            throw new IllegalMove($from, $to);
        }
    }
}
