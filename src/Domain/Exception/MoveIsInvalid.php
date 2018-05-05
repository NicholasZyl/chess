<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Position\Coordinates;

final class MoveIsInvalid extends IllegalMove
{
    /**
     * Create exception for generally invalid move.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     */
    public function __construct(Coordinates $from, Coordinates $to)
    {
        parent::__construct(sprintf('Move between %s and %s is not possible.', $from, $to));
    }
}
