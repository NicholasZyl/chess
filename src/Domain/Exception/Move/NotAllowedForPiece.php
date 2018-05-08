<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Move;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Piece;

class NotAllowedForPiece extends IllegalMove
{
    public function __construct(Piece $piece, NotIntervened $move)
    {
        parent::__construct(
            sprintf('%s from %s to %s is not allowed for %s.', $move, $move->source(), $move->destination(), $piece)
        );
    }
}