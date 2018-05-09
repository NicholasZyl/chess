<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Move;

use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece;

final class NotAllowedForPiece extends IllegalMove
{
    public function __construct(Piece $piece, BoardMove $move)
    {
        parent::__construct(
            sprintf('%s from %s to %s is not allowed for %s %s.', $move, $move->source(), $move->destination(), $piece->color(), $piece)
        );
    }
}