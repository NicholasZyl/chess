<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\BoardException;

final class InvalidDirection extends BoardException
{
    /**
     * Create exception for two coordinates not on same direction.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     * @param Direction $direction
     */
    public function __construct(Coordinates $from, Coordinates $to, Direction $direction)
    {
        parent::__construct(sprintf('%s and %s are not %s.', $from, $to, $direction));
    }
}
