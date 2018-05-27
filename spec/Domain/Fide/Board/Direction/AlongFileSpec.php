<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use PhpSpec\ObjectBehavior;

class AlongFileSpec extends ObjectBehavior
{
    function it_is_direction()
    {
        $this->shouldBeAnInstanceOf(Direction::class);
    }

    function it_calculates_coordinates_to_next_adjacent_higher_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('a', 5);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('a', 3));
    }

    function it_calculates_coordinates_to_next_adjacent_lower_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('a', 1);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('a', 1));
    }

    function it_calculates_next_coordinates_even_if_not_on_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('b', 1);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('a', 1));
    }

    function it_cannot_calculate_next_coordinates_if_already_on_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('c', 2);

        $this->shouldThrow(new CoordinatesNotReachable($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }

    function it_is_same_direction_if_along_file()
    {
        $this->inSameDirectionAs(new AlongFile())->shouldBe(true);
    }

    function it_is_not_same_direction_if_not_along_file()
    {
        $this->inSameDirectionAs(new AlongDiagonal())->shouldBe(false);
    }

    function it_calculates_rank_distance_between_two_coordinates_on_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('a', 4);
        $to = CoordinatePair::fromFileAndRank('a', 2);

        $this->distanceBetween($from, $to)->shouldBe(2);
    }
}
