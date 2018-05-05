<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Position\Coordinates;
use NicholasZyl\Chess\Domain\Move;

final class MoveToOccupiedPosition extends IllegalMove
{
    /**
     * Create exception for move to position occupied by piece of the same color.
     *
     * @param Move $move
     * @param Coordinates $destinationPosition
     */
    public function __construct(Move $move, Coordinates $destinationPosition)
    {
        parent::__construct(
            sprintf('Move between %s and %s is illegal because %s is occupied by a piece of same color.', $move->from(), $move->to(), $destinationPosition)
        );
    }
}
