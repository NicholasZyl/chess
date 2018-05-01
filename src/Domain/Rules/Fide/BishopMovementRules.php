<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\MovementRules;

final class BishopMovementRules implements MovementRules
{
    /**
     * {@inheritdoc}
     */
    public function isFor(): Rank
    {
        return Rank::bishop();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Coordinates $from, Coordinates $to): void
    {
        // TODO: Implement validate() method.
    }
}
