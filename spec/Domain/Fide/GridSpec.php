<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Fide\Grid;
use NicholasZyl\Chess\Domain\Fide\Square;
use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
use PhpSpec\ObjectBehavior;

class GridSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Grid::class);
    }

    function it_prepares_grid_of_sixty_four_squares()
    {
        $grid = $this->squares();
        $grid->shouldBeArray();
        $grid->shouldHaveCount(64);
    }

    function it_prepares_grid_eight_by_eight_squares()
    {
        $grid = $this->squares();
        $grid[0]->shouldBeLike(Square::forCoordinates(CoordinatePair::fromFileAndRank('a', 1)));
        $grid[7]->shouldBeLike(Square::forCoordinates(CoordinatePair::fromFileAndRank('a', 8)));
        $grid[56]->shouldBeLike(Square::forCoordinates(CoordinatePair::fromFileAndRank('h', 1)));
        $grid[63]->shouldBeLike(Square::forCoordinates(CoordinatePair::fromFileAndRank('h', 8)));
    }
}
