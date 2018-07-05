<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use PhpSpec\ObjectBehavior;

class AlongRankSpec extends ObjectBehavior
{
    function it_is_direction()
    {
        $this->shouldBeAnInstanceOf(Direction::class);
    }

    function it_calculates_coordinates_to_next_adjacent_kingside_file()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('c', 2);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('b', 2));
    }

    function it_calculates_coordinates_to_next_adjacent_queenside_file()
    {
        $from = CoordinatePair::fromFileAndRank('b', 1);
        $to = CoordinatePair::fromFileAndRank('a', 1);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('a', 1));
    }

    function it_calculates_next_coordinates_even_if_not_on_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('b', 1);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('b', 2));
    }

    function it_cannot_calculate_next_coordinates_if_already_on_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('a', 1);

        $this->shouldThrow(new CoordinatesNotReachable($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }

    function it_is_same_direction_if_along_rank()
    {
        $this->inSameDirectionAs(new AlongRank())->shouldBe(true);
    }

    function it_is_not_same_direction_if_not_along_diagonal()
    {
        $this->inSameDirectionAs(new AlongFile())->shouldBe(false);
    }

    function it_calculates_file_distance_between_two_coordinates_on_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('e', 1);
        $to = CoordinatePair::fromFileAndRank('g', 1);

        $this->distanceBetween($from, $to)->shouldBe(2);
    }

    function it_calculates_next_coordinates_from_given_point_towards_kingside()
    {
        $this->beConstructedWith(true);

        $position = CoordinatePair::fromFileAndRank('e', 5);

        $this->nextAlongFrom($position)->shouldBeLike(CoordinatePair::fromFileAndRank('f', 5));
    }

    function it_calculates_next_coordinates_from_given_point_towards_queenside()
    {
        $this->beConstructedWith(false);

        $position = CoordinatePair::fromFileAndRank('e', 5);

        $this->nextAlongFrom($position)->shouldBeLike(CoordinatePair::fromFileAndRank('d', 5));
    }
}
