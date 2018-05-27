<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\BoardException;

final class CoordinatesNotReachable extends BoardException
{
    /**
     * Create exception for coordinates that are not reachable along given direction.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     * @param Direction $direction
     */
    public function __construct(Coordinates $from, Coordinates $to, Direction $direction)
    {
        parent::__construct(sprintf('%s is not reachable from %s %s', $to, $from, $direction));
    }
}
