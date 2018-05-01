<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\MovementRules;

final class KingMovementRules implements MovementRules
{
    /**
     * {@inheritdoc}
     */
    public function isFor(): Rank
    {
        return Rank::king();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Coordinates $from, Coordinates $to): void
    {
        if ($from->rankDistance($to) > 1 || $from->fileDistance($to) > 1) {
            throw new IllegalMove($from, $to);
        }
    }
}
