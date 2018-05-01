<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\MovementRules;

final class KingMovementRules implements MovementRules
{
    /**
     * {@inheritdoc}
     */
    public function forRank(): Rank
    {
        return Rank::king();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Color $color, Coordinates $from, Coordinates $to): void
    {
        if ($from->distance($to)->isHigherThan(1)) {
            throw new IllegalMove($from, $to);
        }
    }
}
