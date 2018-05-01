<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\MovementRules;

final class BishopMovementRules implements MovementRules
{
    /**
     * For which rank given rule is.
     *
     * @return Rank
     */
    public function isFor(): Rank
    {
        return Rank::bishop();
    }

    /**
     * Check if proposed move from one coordinate to another is legal.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @throws IllegalMove
     * @return void
     */
    public function validate(Coordinates $from, Coordinates $to): void
    {
        // TODO: Implement validate() method.
    }
}
