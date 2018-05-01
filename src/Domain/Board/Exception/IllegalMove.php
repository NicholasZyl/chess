<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;

final class IllegalMove extends \RuntimeException
{
    public function __construct(Coordinates $from, Coordinates $to)
    {
        parent::__construct(sprintf('Move from %s to %s is illegal', $from, $to));
    }
}
