<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\ChessboardMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use PhpSpec\ObjectBehavior;

class AlongRankSpec extends ObjectBehavior
{
    function it_is_chessboard_move()
    {
        $this->shouldBeAnInstanceOf(ChessboardMove::class);
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

        $this->count()->shouldBe(2);
        $this->current()->shouldBeLike(CoordinatePair::fromFileAndRank('c', 1));
        $this->next();
        $this->current()->shouldBeLike($to);
    }
}
