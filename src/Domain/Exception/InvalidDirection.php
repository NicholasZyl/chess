<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;

final class InvalidDirection extends \RuntimeException
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
