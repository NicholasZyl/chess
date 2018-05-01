<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Distance;
use PhpSpec\ObjectBehavior;

class CoordinatesSpec extends ObjectBehavior
{
    function it_can_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['a1']);
        $this->shouldHaveType(Coordinates::class);
    }

    function it_can_be_created_from_file_and_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $this->shouldHaveType(Coordinates::class);
    }

    function it_can_be_converted_to_string()
    {
        $this->beConstructedThrough('fromString', ['a1']);
        $this->__toString()->shouldBe('a1');
    }

    function it_calculates_distance_between_coordinates()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1,]);
        $anotherCoordinate = Coordinates::fromFileAndRank('a', 1);

        $this->distance($anotherCoordinate)->shouldBeLike(Distance::calculate($this->getWrappedObject(), $anotherCoordinate));
    }

    function it_knows_file_and_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $this->file()->shouldBe('a');
        $this->rank()->shouldBe(1);
    }
}
