<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Board;

use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\Board\UnknownDirection;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use PhpSpec\ObjectBehavior;

class CoordinatePairSpec extends ObjectBehavior
{
    function it_can_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['a1']);
        $this->shouldHaveType(CoordinatePair::class);
    }

    function it_can_be_created_from_file_and_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $this->shouldHaveType(CoordinatePair::class);
    }

    function it_cannot_be_created_for_malformed_string()
    {
        $this->beConstructedThrough('fromString', ['wrong2']);
        $this->shouldThrow(new \InvalidArgumentException('"wrong2" is not a proper format for coordinates.'))
            ->duringInstantiation();
    }

    function it_cannot_be_created_for_coordinates_outside_of_board()
    {
        $this->beConstructedThrough('fromFileAndRank', ['i', 1,]);
        $this->shouldThrow(OutOfBoard::class)
            ->duringInstantiation();
    }

    function it_can_be_converted_to_string()
    {
        $this->beConstructedThrough('fromString', ['a1']);
        $this->__toString()->shouldBe('a1');
    }

    function it_has_file_and_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $this->file()->shouldBe('a');
        $this->rank()->shouldBe(1);
    }

    function it_equals_other_pair_if_have_same_rank_and_file()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $other = CoordinatePair::fromFileAndRank('a', 1);

        $this->equals($other)->shouldBe(true);
    }

    function it_does_not_equal_other_pair_if_have_different_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $other = CoordinatePair::fromFileAndRank('a', 8);

        $this->equals($other)->shouldBe(false);
    }

    function it_does_not_equal_other_pair_if_have_different_file()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $other = CoordinatePair::fromFileAndRank('b', 1);

        $this->equals($other)->shouldBe(false);
    }

    function it_does_not_equal_null()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);

        $this->equals(null)->shouldBe(false);
    }

    function it_calculates_next_coordinates_towards_destination_in_given_direction()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $destination = CoordinatePair::fromFileAndRank('d', 1);

        $this->nextTowards($destination, new AlongRank())->shouldBeLike(CoordinatePair::fromFileAndRank('b', 1));
    }

    function it_knows_that_other_coordinates_on_same_file_has_direction_along_file()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 5]);
        $coordinates = CoordinatePair::fromFileAndRank('a', 1);

        $this->directionTo($coordinates)->shouldBeLike(new AlongFile());
    }

    function it_knows_that_other_coordinates_on_same_rank_has_direction_along_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['g', 3]);
        $coordinates = CoordinatePair::fromFileAndRank('a', 3);

        $this->directionTo($coordinates)->shouldBeLike(new AlongRank());
    }

    function it_knows_that_other_coordinates_on_same_diagonal_has_direction_along_diagonal()
    {
        $this->beConstructedThrough('fromFileAndRank', ['c', 5]);
        $coordinates = CoordinatePair::fromFileAndRank('g', 1);

        $this->directionTo($coordinates)->shouldBeLike(new AlongDiagonal());
    }

    function it_does_not_know_direction_if_is_not_along_any_line()
    {
        $this->beConstructedThrough('fromFileAndRank', ['c', 5]);
        $coordinates = CoordinatePair::fromFileAndRank('a', 1);

        $this->shouldThrow(new UnknownDirection($this->getWrappedObject(), $coordinates))->during('directionTo', [$coordinates,]);
    }

    function it_knows_distance_along_direction()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $destination = CoordinatePair::fromFileAndRank('d', 1);

        $this->distanceTo($destination, new AlongRank())->shouldBe(3);
    }
}
