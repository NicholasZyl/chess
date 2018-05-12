<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\BoardException;

final class UnknownDirection extends BoardException
{
    /**
     * Create exception for unknown direction between two coordinates.
     *
     * @param Coordinates $source
     * @param Coordinates $destination
     */
    public function __construct(Coordinates $source, Coordinates $destination)
    {
        parent::__construct(sprintf('Direction between %s and %s is unknown.', $source, $destination));
    }
}