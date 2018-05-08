<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Board\Direction;

use NicholasZyl\Chess\Domain\Board\Direction\Direction;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class ForwardSpec extends ObjectBehavior
{
    function it_is_direction(Direction $direction)
    {
        $this->beConstructedWith(Color::white(), $direction);

        $this->shouldBeAnInstanceOf(Direction::class);
    }

    function it_calculates_coordinates_to_next_adjacent_higher_rank_for_white()
    {
        $this->beConstructedWith(Color::white(), new AlongFile());

        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('a', 5);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('a', 3));
    }

    function it_cannot_calculate_next_coordinates_to_lower_rank_for_white()
    {
        $this->beConstructedWith(Color::white(), new AlongFile());

        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('a', 1);

        $this->shouldThrow(new InvalidDirection($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }

    function it_calculates_coordinates_to_next_adjacent_lower_rank_for_black()
    {
        $this->beConstructedWith(Color::black(), new AlongFile());

        $from = CoordinatePair::fromFileAndRank('a', 3);
        $to = CoordinatePair::fromFileAndRank('a', 1);

        $this->nextCoordinatesTowards($from, $to)->shouldBeLike(CoordinatePair::fromFileAndRank('a', 2));
    }

    function it_cannot_calculate_next_coordinates_to_higher_rank_for_black()
    {
        $this->beConstructedWith(Color::black(), new AlongFile());

        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 2);

        $this->shouldThrow(new InvalidDirection($from, $to, $this->getWrappedObject()))->during('nextCoordinatesTowards', [$from, $to,]);
    }
}
