<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Move;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Move;

final class Prevented extends IllegalMove
{
    public function __construct(Move $move)
    {
        parent::__construct(sprintf('%s is prevented.', $move));
    }
}