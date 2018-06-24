<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;

final class MoveToOccupiedPosition extends IllegalAction
{
    /**
     * Create exception for move to position occupied by piece of the same color.
     *
     * @param Coordinates $destinationPosition
     */
    public function __construct(Coordinates $destinationPosition)
    {
        parent::__construct(
            sprintf('Move is illegal because %s is occupied by a piece of same color.', $destinationPosition)
        );
    }
}
