<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

final class Coordinates
{
    private function __construct()
    {
    }

    public static function fromString(string $coordinates)
    {
        $coordinates = new Coordinates();

        // TODO: write logic here

        return $coordinates;
    }
}
