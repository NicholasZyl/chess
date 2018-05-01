<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Distance;
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
        $this->beConstructedThrough('calculate', [$from, $to,]);

        $this->isHigherThan(0)->shouldBe(false);
    }

    function it_knows_when_rank_distance_is_higher_than_one()
    {
        $from = Coordinates::fromFileAndRank('a', 1);
        $to = Coordinates::fromFileAndRank('a', 3);
        $this->beConstructedThrough('calculate', [$from, $to,]);

        $this->isHigherThan(1)->shouldBe(true);
    }

    function it_knows_when_file_distance_is_higher_than_one()
    {
        $from = Coordinates::fromFileAndRank('c', 1);
        $to = Coordinates::fromFileAndRank('a', 1);
        $this->beConstructedThrough('calculate', [$from, $to,]);

        $this->isHigherThan(1)->shouldBe(true);
    }

    function it_knows_when_distance_is_vertical()
    {
        $from = Coordinates::fromFileAndRank('a', 1);
        $to = Coordinates::fromFileAndRank('a', 3);
        $this->beConstructedThrough('calculate', [$from, $to,]);

        $this->shouldBeVertical();
        $this->shouldNotBeHorizontal();
        $this->shouldNotBeDiagonal();
    }
}
