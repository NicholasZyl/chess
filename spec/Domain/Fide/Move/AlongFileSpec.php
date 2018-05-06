<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class AlongFileSpec extends ObjectBehavior
{
    function it_is_chess_move()
    {
        $this->shouldBeAnInstanceOf(Move::class);
    }

    function it_cannot_be_made_between_coordinates_not_on_the_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('c', 1);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldThrow(new \InvalidArgumentException('c1 and b1 are not along the same file.'))->duringInstantiation();
    }

    function it_is_collection_of_steps_to_make_move_between_coordinates_along_file()
    {
        $from = CoordinatePair::fromFileAndRank('b', 1);
        $to = CoordinatePair::fromFileAndRank('b', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->steps()->shouldBeLike(
            [
                CoordinatePair::fromFileAndRank('b', 2),
                CoordinatePair::fromFileAndRank('b', 3),
            ]
        );
    }

    function it_is_towards_opponent_rank_for_white_if_moving_to_higher_rank()
    {
        $from = CoordinatePair::fromFileAndRank('f', 1);
        $to = CoordinatePair::fromFileAndRank('f', 5);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isTowardsOpponentSideFor(Color::white())->shouldBe(true);
    }

    function it_is_not_towards_opponent_rank_for_white_if_moving_to_lower_rank()
    {
        $from = CoordinatePair::fromFileAndRank('d', 5);
        $to = CoordinatePair::fromFileAndRank('d', 3);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isTowardsOpponentSideFor(Color::white())->shouldBe(false);
    }

    function it_is_towards_opponent_rank_for_black_if_moving_to_lower_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 5);
        $to = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isTowardsOpponentSideFor(Color::black())->shouldBe(true);
    }

    function it_is_not_towards_opponent_rank_for_black_if_moving_to_higher_rank()
    {
        $from = CoordinatePair::fromFileAndRank('c', 1);
        $to = CoordinatePair::fromFileAndRank('c', 5);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isTowardsOpponentSideFor(Color::black())->shouldBe(false);
    }
}
