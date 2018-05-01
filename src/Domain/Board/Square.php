<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

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
}
