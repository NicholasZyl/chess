<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\ChessboardMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use PhpSpec\ObjectBehavior;

class NearestNotSameFileRankOrDiagonalSpec extends ObjectBehavior
{
    function it_is_chessboard_move()
    {
        $this->shouldBeAnInstanceOf(ChessboardMove::class);
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

    function it_consists_only_target_coordinates_as_a_step_to_move_to_the_nearest_square()
    {
        $from = CoordinatePair::fromFileAndRank('d', 2);
        $to = CoordinatePair::fromFileAndRank('e', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->count()->shouldBe(1);
        $this->current()->shouldBeLike($to);
    }
}
