<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Move;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece;

class ToIllegalPosition extends IllegalMove
{
    public function __construct(Piece $piece, Coordinates $source, Coordinates $destination)
    {
        parent::__construct(sprintf('%s is illegal position for %s standing at %s', $destination, $piece, $source));
    }
}