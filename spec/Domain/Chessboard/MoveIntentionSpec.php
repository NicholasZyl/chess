<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Exception\MoveIsInvalid;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongFile;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongRank;
use NicholasZyl\Chess\Domain\Chessboard\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\MoveIntention;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use PhpSpec\ObjectBehavior;

class MoveIntentionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MoveIntention::class);
    }

    function it_plans_move_along_file_for_coordinates_along_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 2);

        $this->intentMove($from, $to)->shouldBeLike(AlongFile::between($from, $to));
    }

    function it_plans_move_along_rank_for_coordinates_along_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('c', 1);
        $to = CoordinatePair::fromFileAndRank('a', 1);

        $this->intentMove($from, $to)->shouldBeLike(AlongRank::between($from, $to));
    }

    function it_plans_move_along_diagonal_for_coordinates_along_same_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('d', 4);
        $to = CoordinatePair::fromFileAndRank('a', 1);

        $this->intentMove($from, $to)->shouldBeLike(AlongDiagonal::between($from, $to));
    }

    function it_plans_nearest_square_move_not_on_same_file_rank_or_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('c', 2);

        $this->intentMove($from, $to)->shouldBeLike(NearestNotSameFileRankOrDiagonal::between($from, $to));
    }

    function it_is_not_possible_move_if_cannot_intent_known_type_of_moves()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('c', 6);

        $this->shouldThrow(new MoveIsInvalid($from, $to))->during('intentMove', [$from, $to,]);
    }
}
