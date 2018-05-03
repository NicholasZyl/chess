<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\ChessboardMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use PhpSpec\ObjectBehavior;

class AlongDiagonalSpec extends ObjectBehavior
{
    function it_is_chessboard_move()
    {
        $this->shouldBeAnInstanceOf(ChessboardMove::class);
    }

    function it_cannot_be_made_between_coordinates_not_on_the_same_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('c', 1);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldThrow(new \InvalidArgumentException('c1 and b1 are not along the same diagonal.'))->duringInstantiation();
    }

    function it_is_collection_of_steps_to_make_move_between_coordinates_along_diagonal_rising_slope_forwards()
    {
        $from = CoordinatePair::fromFileAndRank('b', 1);
        $to = CoordinatePair::fromFileAndRank('d', 3);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->count()->shouldBe(2);
        $this->current()->shouldBeLike(CoordinatePair::fromFileAndRank('c', 2));
        $this->next();
        $this->current()->shouldBeLike($to);
    }

    function it_is_collection_of_steps_to_make_move_between_coordinates_along_diagonal_rising_slope_backwards()
    {
        $from = CoordinatePair::fromFileAndRank('d', 5);
        $to = CoordinatePair::fromFileAndRank('c', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->count()->shouldBe(1);
        $this->current()->shouldBeLike($to);
    }

    function it_is_collection_of_steps_to_make_move_between_coordinates_along_diagonal_falling_slope_forwards()
    {
        $from = CoordinatePair::fromFileAndRank('f', 1);
        $to = CoordinatePair::fromFileAndRank('b', 5);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->count()->shouldBe(4);
        $this->current()->shouldBeLike(CoordinatePair::fromFileAndRank('e', 2));
        $this->next();
        $this->current()->shouldBeLike(CoordinatePair::fromFileAndRank('d', 3));
        $this->next();
        $this->current()->shouldBeLike(CoordinatePair::fromFileAndRank('c', 4));
        $this->next();
        $this->current()->shouldBeLike($to);
    }

    function it_is_collection_of_steps_to_make_move_between_coordinates_along_diagonal_slope_backwards()
    {
        $from = CoordinatePair::fromFileAndRank('d', 5);
        $to = CoordinatePair::fromFileAndRank('f', 3);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->count()->shouldBe(2);
        $this->current()->shouldBeLike(CoordinatePair::fromFileAndRank('e', 4));
        $this->next();
        $this->current()->shouldBeLike($to);
    }
}
