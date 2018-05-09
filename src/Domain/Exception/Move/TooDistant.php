<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Move;

use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;

final class TooDistant extends IllegalMove
{
    public function __construct(BoardMove $move)
    {
        parent::__construct(sprintf('%s from %s to %s is too distant.', $move, $move->source(), $move->destination()));
    }
}