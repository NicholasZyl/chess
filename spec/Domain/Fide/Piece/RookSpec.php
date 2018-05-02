<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
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

    function it_can_move_to_any_square_along_rank_forward()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('f', 3);

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_rank_backward()
    {
        $from = Coordinates::fromFileAndRank('d', 3);
        $to = Coordinates::fromFileAndRank('c', 3);

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_file_queenside()
    {
        $from = Coordinates::fromFileAndRank('f', 3);
        $to = Coordinates::fromFileAndRank('f', 2);

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_file_kingside()
    {
        $from = Coordinates::fromFileAndRank('d', 3);
        $to = Coordinates::fromFileAndRank('d', 6);

        $this->intentMove($from, $to);
    }

    function it_cannot_move_along_diagonal()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('d', 4);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_cannot_move_to_other_square()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('g', 4);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }
}
