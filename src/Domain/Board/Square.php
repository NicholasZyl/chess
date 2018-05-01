<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;

final class Square
{
    private function __construct()
    {
    }

    public static function forCoordinates(Coordinates $coordinates)
    {
        $square = new Square();

        // TODO: write logic here

        return $square;
    }

    public function pickPiece(): Piece
    {
        return Piece::fromRankAndColor(Piece\Rank::fromString('queen'), Color::black());
    }
}
