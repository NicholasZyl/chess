<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Board\Direction;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use PhpSpec\ObjectBehavior;

class AlongDiagonalSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AlongDiagonal::class);
    }

    function it_is_direction()
    {
        $this->shouldBeAnInstanceOf(Direction::class);
    }

    function it_calculates_coordinates_to_next_adjacent_coordinates_along_diagonal_rising_slope_forwards()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('c', 4);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('b', 3));
    }

    function it_calculates_coordinates_to_next_adjacent_coordinates_along_diagonal_rising_slope_backwards()
    {
        $from = CoordinatePair::fromFileAndRank('f', 5);
        $to = CoordinatePair::fromFileAndRank('d', 3);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('e', 4));
    }

    function it_calculates_coordinates_to_next_adjacent_coordinates_along_diagonal_falling_slope_forwards()
    {
        $from = CoordinatePair::fromFileAndRank('b', 2);
        $to = CoordinatePair::fromFileAndRank('a', 3);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('a', 3));
    }

    function it_calculates_coordinates_to_next_adjacent_coordinates_along_diagonal_falling_slope_backwards()
    {
        $from = CoordinatePair::fromFileAndRank('a', 3);
        $to = CoordinatePair::fromFileAndRank('c', 1);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('b', 2));
    }

    function it_cannot_calculate_next_coordinates_if_on_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('a', 1);

        $this->shouldThrow(new CoordinatesNotReachable($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }

    function it_cannot_calculate_next_coordinates_if_on_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('b', 2);

        $this->shouldThrow(new CoordinatesNotReachable($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }

    function it_calculates_distance_between_two_coordinates_on_same_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('e', 5);

        $this->distanceBetween($from, $to)->shouldBe(4);
    }

    function it_calculates_next_coordinates_from_given_point_towards_kingside_up()
    {
        $this->beConstructedWith(true, true);

        $position = CoordinatePair::fromFileAndRank('e', 5);

        $this->nextAlongFrom($position)->shouldBeLike(CoordinatePair::fromFileAndRank('f', 6));
    }

    function it_calculates_next_coordinates_from_given_point_towards_kingside_down()
    {
        $this->beConstructedWith(true, false);

        $position = CoordinatePair::fromFileAndRank('e', 5);

        $this->nextAlongFrom($position)->shouldBeLike(CoordinatePair::fromFileAndRank('f', 4));
    }

    function it_calculates_next_coordinates_from_given_point_towards_queenside_up()
    {
        $this->beConstructedWith(false, true);

        $position = CoordinatePair::fromFileAndRank('e', 5);

        $this->nextAlongFrom($position)->shouldBeLike(CoordinatePair::fromFileAndRank('d', 6));
    }

    function it_calculates_next_coordinates_from_given_point_towards_queenside_down()
    {
        $this->beConstructedWith(false, false);

        $position = CoordinatePair::fromFileAndRank('e', 5);

        $this->nextAlongFrom($position)->shouldBeLike(CoordinatePair::fromFileAndRank('d', 4));
    }
}
