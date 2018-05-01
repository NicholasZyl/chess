<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Piece;

final class Rank
{
    private function __construct()
    {
    }

    public static function fromString(string $rankName)
    {
        $rank = new Rank();

        // TODO: write logic here

        return $rank;
    }
}
