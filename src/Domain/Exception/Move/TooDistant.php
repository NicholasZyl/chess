<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Move;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;

final class TooDistant extends IllegalMove
{
    public function __construct(Coordinates $source, Coordinates $destination, int $allowedDistance)
    {
        parent::__construct(sprintf('Move from %s to %s is further than %d square%s away.', $source, $destination, $allowedDistance, $allowedDistance > 1 ? 's' : ''));
    }
}