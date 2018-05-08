<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use PhpSpec\ObjectBehavior;

class LShapedSpec extends ObjectBehavior
{
    function it_is_direction()
    {
        $this->shouldBeAnInstanceOf(Direction::class);
    }

    function it_calculates_coordinates_to_nearest_one_not_on_same_file_rank_or_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('c', 3);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('c', 3));
    }

    function it_calculates_coordinates_to_nearest_one_not_on_same_file_rank_or_diagonal_in_any_direction()
    {
        $from = CoordinatePair::fromFileAndRank('f', 5);
        $to = CoordinatePair::fromFileAndRank('g', 3);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('g', 3));
    }

    function it_cannot_calculate_next_coordinates_if_further_than_nearest()
    {
        $from = CoordinatePair::fromFileAndRank('f', 5);
        $to = CoordinatePair::fromFileAndRank('a', 1);

        $this->shouldThrow(new InvalidDirection($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }

    function it_cannot_calculate_next_coordinates_on_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('b', 3);
        $to = CoordinatePair::fromFileAndRank('b', 2);

        $this->shouldThrow(new InvalidDirection($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }

    function it_cannot_calculate_next_coordinates_on_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('b', 2);

        $this->shouldThrow(new InvalidDirection($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }

    function it_cannot_calculate_next_coordinates_on_same_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('b', 3);

        $this->shouldThrow(new InvalidDirection($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }
}
