<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Piece\Rank;

class Piece
{
    private function __construct()
    {
    }

    public static function fromRankAndColor(Rank $rank, Color $color)
    {
        $piece = new Piece();

        // TODO: write logic here

        return $piece;
    }
}
