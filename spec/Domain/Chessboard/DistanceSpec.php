<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Distance;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class DistanceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Distance::class);
    }

    function it_knows_when_both_rank_and_file_distance_is_equal_zero()
    {
        $from = Coordinates::fromFileAndRank('a', 1);
        $to = Coordinates::fromFileAndRank('a', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isHigherThan(0)->shouldBe(false);
    }

    function it_knows_when_rank_distance_is_higher_than_one()
    {
        $from = Coordinates::fromFileAndRank('a', 1);
        $to = Coordinates::fromFileAndRank('a', 3);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isHigherThan(1)->shouldBe(true);
    }

    function it_knows_when_file_distance_is_higher_than_one()
    {
        $from = Coordinates::fromFileAndRank('c', 1);
        $to = Coordinates::fromFileAndRank('a', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isHigherThan(1)->shouldBe(true);
    }

    function it_knows_when_distance_is_vertical()
    {
        $from = Coordinates::fromFileAndRank('a', 1);
        $to = Coordinates::fromFileAndRank('a', 3);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldBeVertical();
        $this->shouldNotBeHorizontal();
        $this->shouldNotBeDiagonal();
    }

    function it_knows_when_distance_is_horizontal()
    {
        $from = Coordinates::fromFileAndRank('d', 4);
        $to = Coordinates::fromFileAndRank('a', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldNotBeVertical();
        $this->shouldBeHorizontal();
        $this->shouldNotBeDiagonal();
    }

    function it_knows_when_distance_is_diagonal()
    {
        $from = Coordinates::fromFileAndRank('a', 1);
        $to = Coordinates::fromFileAndRank('f', 6);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldNotBeVertical();
        $this->shouldNotBeHorizontal();
        $this->shouldBeDiagonal();
    }

    function it_knows_when_vertical_move_was_forward_for_whites()
    {
        $from = Coordinates::fromFileAndRank('a', 1);
        $to = Coordinates::fromFileAndRank('a', 2);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isForward(Color::white())->shouldBe(true);
    }

    function it_knows_when_vertical_move_was_backward_for_whites()
    {
        $from = Coordinates::fromFileAndRank('c', 5);
        $to = Coordinates::fromFileAndRank('c', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isForward(Color::white())->shouldBe(false);
    }

    function it_knows_when_vertical_move_was_forward_for_blacks()
    {
        $from = Coordinates::fromFileAndRank('b', 3);
        $to = Coordinates::fromFileAndRank('b', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isForward(Color::black())->shouldBe(true);
    }

    function it_knows_when_vertical_move_was_backward_for_blacks()
    {
        $from = Coordinates::fromFileAndRank('d', 6);
        $to = Coordinates::fromFileAndRank('d', 7);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->isForward(Color::black())->shouldBe(false);
    }
}
