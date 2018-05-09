<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;

final class MoveToUnoccupiedPosition extends IllegalMove
{
    /**
     * Create exception for move to position occupied by piece of the same color.
     *
     * @param Coordinates $destinationPosition
     */
    public function __construct(Coordinates $destinationPosition)
    {
        parent::__construct(
            sprintf('Move is illegal because %s is not occupied by a piece of opponent\'s color.', $destinationPosition)
        );
    }
}
