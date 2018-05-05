<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Square;

use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
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

    function it_cannot_be_created_for_too_low_file()
    {
        $this->beConstructedThrough('fromFileAndRank', ['Z', 1,]);
        $this->shouldThrow(new \InvalidArgumentException('"Z" is not a proper file.'))
            ->duringInstantiation();
    }

    function it_cannot_be_created_for_too_high_file()
    {
        $this->beConstructedThrough('fromFileAndRank', ['i', 1,]);
        $this->shouldThrow(new \InvalidArgumentException('"i" is not a proper file.'))
            ->duringInstantiation();
    }

    function it_cannot_be_created_for_too_low_rank()
    {
        $this->beConstructedThrough('fromFileAndRank', ['d', 0,]);
        $this->shouldThrow(new \InvalidArgumentException('"0" is not a proper rank.'))
            ->duringInstantiation();
    }

    function it_cannot_be_created_for_too_high_rank()
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

    function it_knows_file_and_rank()
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

    function it_knows_when_is_on_same_file_with_other_pair()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $other = CoordinatePair::fromFileAndRank('a', 4);

        $this->isOnSameFile($other)->shouldBe(true);
    }

    function it_knows_when_is_not_on_same_file_with_other_pair()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $other = CoordinatePair::fromFileAndRank('b', 4);

        $this->isOnSameFile($other)->shouldBe(false);
    }

    function it_knows_when_is_on_same_rank_with_other_pair()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $other = CoordinatePair::fromFileAndRank('b', 1);

        $this->isOnSameRank($other)->shouldBe(true);
    }

    function it_knows_when_is_not_on_same_rank_with_other_pair()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 3]);
        $other = CoordinatePair::fromFileAndRank('b', 4);

        $this->isOnSameRank($other)->shouldBe(false);
    }

    function it_knows_when_is_on_same_diagonal_rising_slope_with_other_pair()
    {
        $this->beConstructedThrough('fromFileAndRank', ['a', 1]);
        $other = CoordinatePair::fromFileAndRank('c', 3);

        $this->isOnSameDiagonal($other)->shouldBe(true);
    }

    function it_knows_when_is_on_same_diagonal_falling_slope_with_other_pair()
    {
        $this->beConstructedThrough('fromFileAndRank', ['b', 6]);
        $other = CoordinatePair::fromFileAndRank('e', 3);

        $this->isOnSameDiagonal($other)->shouldBe(true);
    }

    function it_knows_when_is_not_along_same_diagonal_with_other_pair()
    {
        $this->beConstructedThrough('fromFileAndRank', ['b', 6]);
        $other = CoordinatePair::fromFileAndRank('e', 2);

        $this->isOnSameDiagonal($other)->shouldBe(false);
    }
}
