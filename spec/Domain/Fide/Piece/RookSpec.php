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

    function it_can_move_to_any_square_along_rank_forward()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('f', 3);

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_rank_backward()
    {
        $from = CoordinatePair::fromFileAndRank('d', 3);
        $to = CoordinatePair::fromFileAndRank('c', 3);

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_file_queenside()
    {
        $from = CoordinatePair::fromFileAndRank('f', 3);
        $to = CoordinatePair::fromFileAndRank('f', 2);

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_file_kingside()
    {
        $from = CoordinatePair::fromFileAndRank('d', 3);
        $to = CoordinatePair::fromFileAndRank('d', 6);

        $this->intentMove($from, $to);
    }

    function it_can_not_move_along_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('d', 4);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_cannot_move_to_other_square()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('g', 4);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }
}
