<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;

final class UnknownDirection extends \RuntimeException
{
    public function __construct(Coordinates $source, Coordinates $destination)
    {
        parent::__construct(sprintf('Direction between %s and %s is unknown.', $source, $destination));
    }
}