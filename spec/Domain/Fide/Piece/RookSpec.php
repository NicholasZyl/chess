<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongFile;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongRank;
use NicholasZyl\Chess\Domain\Chessboard\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class RookSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Rook::class);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_rook_if_same_color()
    {
        $pawn = Rook::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_can_move_along_file()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 2);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->mayMove($move);
    }

    function it_can_move_along_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $move = AlongRank::between(
            $from,
            $to
        );

        $this->mayMove($move);
    }

    function it_cannot_move_along_diagonal()
    {
        $move = AlongDiagonal::between(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->shouldThrow(IllegalMove::forMove($move))->during('mayMove', [$move,]);
    }

    function it_cannot_move_to_nearest_square()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('c', 2);
        $move = NearestNotSameFileRankOrDiagonal::between(
            $from,
            $to
        );

        $this->shouldThrow(IllegalMove::forMove($move))->during('mayMove', [$move,]);
    }
}
