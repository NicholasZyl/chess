<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use PhpSpec\ObjectBehavior;

class CoordinatesSpec extends ObjectBehavior
{
    function it_can_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['A1']);
        $this->shouldHaveType(Coordinates::class);
    }

    function it_can_be_created_from_file_and_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['A', 1]);
        $this->shouldHaveType(Coordinates::class);
    }

    function it_can_be_converted_to_string()
    {
        $this->beConstructedThrough('fromString', ['A1']);
        $this->__toString()->shouldBe('a1');
    }

    function it_has_no_rank_distance_to_coordinates_on_same_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1,]);
        $anotherCoordinate = Coordinates::fromFileAndRank('a', 1);

        $this->rankDistance($anotherCoordinate)->shouldBe(0);
    }

    function it_calculates_distance_to_coordinate_with_higher_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1,]);
        $anotherCoordinate = Coordinates::fromFileAndRank('a', 3);

        $this->rankDistance($anotherCoordinate)->shouldBe(2);
    }

    function it_calculates_distance_to_coordinate_with_lower_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 5,]);
        $anotherCoordinate = Coordinates::fromFileAndRank('a', 1);

        $this->rankDistance($anotherCoordinate)->shouldBe(4);
    }

    function it_has_no_file_distance_to_coordinates_on_same_file()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1,]);
        $anotherCoordinate = Coordinates::fromFileAndRank('a', 1);

        $this->fileDistance($anotherCoordinate)->shouldBe(0);
    }

    function it_calculates_distance_to_coordinate_with_file_more_to_queenside()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1,]);
        $anotherCoordinate = Coordinates::fromFileAndRank('d', 1);

        $this->fileDistance($anotherCoordinate)->shouldBe(3);
    }

    function it_calculates_distance_to_coordinate_with_file_more_to_kingside()
    {
        $this->beConstructedThrough('fromFileAndRank', ['c', 1,]);
        $anotherCoordinate = Coordinates::fromFileAndRank('a', 1);

        $this->fileDistance($anotherCoordinate)->shouldBe(2);
    }
}
