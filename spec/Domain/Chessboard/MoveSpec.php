<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class MoveSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Move::class);
    }

    function it_is_not_possible_to_move_to_the_same_square()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldThrow(new \InvalidArgumentException('It is not possible to move to the same square.'))->duringInstantiation();
    }

    function it_knows_when_is_further_than_one_square_along_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isAwayMoreSquaresThan(1)->shouldBe(true);
    }

    function it_knows_when_is_not_further_than_one_square_along_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 2);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isAwayMoreSquaresThan(1)->shouldBe(false);
    }

    function it_knows_when_is_further_than_two_square_along_file()
    {
        $from = CoordinatePair::fromFileAndRank('d', 1);
        $to = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isAwayMoreSquaresThan(2)->shouldBe(true);
    }

    function it_knows_when_is_not_further_than_one_square_along_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('c', 1);
        $to = CoordinatePair::fromFileAndRank('d', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isAwayMoreSquaresThan(1)->shouldBe(false);
    }

    function it_knows_when_move_is_along_file()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldBeAlongFile();
        $this->shouldNotBeAlongRank();
        $this->shouldNotBeAlongDiagonal();
    }

    function it_knows_when_move_is_along_rank()
    {
        $from = CoordinatePair::fromFileAndRank('d', 4);
        $to = CoordinatePair::fromFileAndRank('a', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldNotBeAlongFile();
        $this->shouldBeAlongRank();
        $this->shouldNotBeAlongDiagonal();
    }

    function it_knows_when_move_is_along_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('f', 6);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldNotBeAlongFile();
        $this->shouldNotBeAlongRank();
        $this->shouldBeAlongDiagonal();
    }

    function it_knows_when_move_was_forward_for_whites()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 2);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isForward(Color::white())->shouldBe(true);
    }

    function it_knows_when_move_was_backward_for_whites()
    {
        $from = CoordinatePair::fromFileAndRank('c', 5);
        $to = CoordinatePair::fromFileAndRank('c', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isForward(Color::white())->shouldBe(false);
    }

    function it_knows_when_move_was_forward_for_blacks()
    {
        $from = CoordinatePair::fromFileAndRank('b', 3);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isForward(Color::black())->shouldBe(true);
    }

    function it_knows_when_move_was_backward_for_blacks()
    {
        $from = CoordinatePair::fromFileAndRank('d', 6);
        $to = CoordinatePair::fromFileAndRank('d', 7);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isForward(Color::black())->shouldBe(false);
    }
}
