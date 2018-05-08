<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class NearestNotSameFileRankOrDiagonalSpec extends ObjectBehavior
{
    function it_is_chess_move()
    {
        $this->shouldBeAnInstanceOf(Move::class);
    }

    function it_cannot_be_made_between_coordinates_on_the_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('c', 1);
        $to = CoordinatePair::fromFileAndRank('c', 2);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldThrow(new \InvalidArgumentException('c1 and c2 are along the same file, rank or diagonal.'))->duringInstantiation();
    }

    function it_cannot_be_made_between_coordinates_on_the_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('c', 1);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldThrow(new \InvalidArgumentException('c1 and b1 are along the same file, rank or diagonal.'))->duringInstantiation();
    }

    function it_cannot_be_made_between_coordinates_on_the_same_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('c', 2);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldThrow(new \InvalidArgumentException('c2 and b1 are along the same file, rank or diagonal.'))->duringInstantiation();
    }

    function it_cannot_be_made_between_coordinates_that_are_not_the_nearest_to_each_other()
    {
        $from = CoordinatePair::fromFileAndRank('c', 2);
        $to = CoordinatePair::fromFileAndRank('f', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldThrow(new \InvalidArgumentException('c2 and f1 are not the nearest squares.'))->duringInstantiation();
    }

    function it_consists_no_steps()
    {
        $from = CoordinatePair::fromFileAndRank('d', 2);
        $to = CoordinatePair::fromFileAndRank('e', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->steps()->shouldBe([]);
    }

    function it_is_towards_opponent_rank_for_white_if_moving_to_higher_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('c', 2);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isTowardsOpponentSideFor(Color::white())->shouldBe(true);
    }

    function it_is_not_towards_opponent_rank_for_white_if_moving_to_lower_rank()
    {
        $from = CoordinatePair::fromFileAndRank('d', 5);
        $to = CoordinatePair::fromFileAndRank('b', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isTowardsOpponentSideFor(Color::white())->shouldBe(false);
    }

    function it_is_towards_opponent_rank_for_black_if_moving_to_lower_rank()
    {
        $from = CoordinatePair::fromFileAndRank('f', 5);
        $to = CoordinatePair::fromFileAndRank('d', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isTowardsOpponentSideFor(Color::black())->shouldBe(true);
    }

    function it_is_not_towards_opponent_rank_for_black_if_moving_to_higher_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 4);
        $to = CoordinatePair::fromFileAndRank('c', 5);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isTowardsOpponentSideFor(Color::black())->shouldBe(false);
    }
}
