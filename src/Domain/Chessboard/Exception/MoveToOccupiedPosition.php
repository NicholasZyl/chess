<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Move;

final class MoveToOccupiedPosition extends IllegalMove
{
    /**
     * Create exception for move to position occupied by piece of the same color.
     *
     * @param Move $move
     * @param CoordinatePair $destinationPosition
     */
    public function __construct(Move $move, CoordinatePair $destinationPosition)
    {
        parent::__construct(
            sprintf('Move between %s and %s is illegal because %s is occupied by a piece of same color.', $move->from(), $move->to(), $destinationPosition)
        );
    }
}
