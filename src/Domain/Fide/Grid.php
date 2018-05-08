<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;

final class Grid
{
    /**
     * Prepare an 8 x 8 grid of 64 squares.
     *
     * @return Square[]
     */
    public function squares(): array
    {
        $grid = [];
        foreach (CoordinatePair::validFiles() as $file) {
            foreach (CoordinatePair::validRanks() as $rank) {
                $coordinates = CoordinatePair::fromFileAndRank($file, $rank);
                $grid[] = Square::forCoordinates($coordinates);
            }
        }

        return $grid;
    }
}
