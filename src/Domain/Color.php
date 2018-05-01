<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

final class Color
{
    private function __construct()
    {
    }

    public static function fromString(string $colorName)
    {
        $color = new Color();

        // TODO: write logic here

        return $color;
    }
}
