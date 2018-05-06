<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class AlongRankSpec extends ObjectBehavior
{
    function it_is_chess_move()
    {
        $this->shouldBeAnInstanceOf(Move::class);
    }

    function it_cannot_be_made_between_coordinates_not_on_the_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('c', 1);
        $to = CoordinatePair::fromFileAndRank('b', 2);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldThrow(new \InvalidArgumentException('c1 and b2 are not along the same rank.'))->duringInstantiation();
    }

    function it_is_collection_of_steps_to_make_move_between_coordinates_along_rank()
    {
        $from = CoordinatePair::fromFileAndRank('b', 1);
        $to = CoordinatePair::fromFileAndRank('d', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->steps()->shouldBeLike(
            [CoordinatePair::fromFileAndRank('c', 1),]
        );
    }

    function it_is_never_towards_opponent_rank()
    {
        $from = CoordinatePair::fromFileAndRank('d', 5);
        $to = CoordinatePair::fromFileAndRank('b', 5);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isTowardsOpponentSideFor(Color::black())->shouldBe(false);
        $this->isTowardsOpponentSideFor(Color::white())->shouldBe(false);
    }
}
