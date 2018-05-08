<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;

final class InvalidDirection extends \RuntimeException
{
    /**
     * Create exception
     *
     * @param Coordinates $from
     * @param Coordinates $to
     * @param string $directionDescription
     */
    public function __construct(Coordinates $from, Coordinates $to, string $directionDescription)
    {
        parent::__construct(sprintf('%s and %s are not %s.', $from, $to, $directionDescription));
    }
}
