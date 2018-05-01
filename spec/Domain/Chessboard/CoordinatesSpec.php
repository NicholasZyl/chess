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

    function it_cannot_be_created_for_malformed_string()
    {
        $this->beConstructedThrough('fromString', ['wrong2']);
        $this->shouldThrow(new \InvalidArgumentException('"wrong2" is not a proper format for coordinates.'))
            ->duringInstantiation();
    }

    function it_cannot_be_created_for_too_small_file()
    {
        $this->beConstructedThrough('fromFileAndRank', ['Z', 1,]);
        $this->shouldThrow(new \InvalidArgumentException('"Z" is not a proper file.'))
            ->duringInstantiation();
    }

    function it_cannot_be_created_for_too_big_file()
    {
        $this->beConstructedThrough('fromFileAndRank', ['i', 1,]);
        $this->shouldThrow(new \InvalidArgumentException('"i" is not a proper file.'))
            ->duringInstantiation();
    }

    function it_cannot_be_created_for_too_small_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['d', 0,]);
        $this->shouldThrow(new \InvalidArgumentException('"0" is not a proper rank.'))
            ->duringInstantiation();
    }

    function it_cannot_be_created_for_too_big_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['d', 9,]);
        $this->shouldThrow(new \InvalidArgumentException('"9" is not a proper rank.'))
            ->duringInstantiation();
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
