<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class BishopSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Bishop::class);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_bishop_if_same_color()
    {
        $pawn = Bishop::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_can_move_to_any_square_along_diagonal_forward_kingside()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('f', 6);

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_diagonal_forward_queenside()
    {
        $from = Coordinates::fromFileAndRank('d', 4);
        $to = Coordinates::fromFileAndRank('a', 7);

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_diagonal_backward_kingside()
    {
        $from = Coordinates::fromFileAndRank('d', 4);
        $to = Coordinates::fromFileAndRank('e', 3);

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_diagonal_backward_queenside()
    {
        $from = Coordinates::fromFileAndRank('d', 4);
        $to = Coordinates::fromFileAndRank('b', 2);

        $this->intentMove($from, $to);
    }

    function it_cannot_move_along_same_rank()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('f', 3);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_cannot_move_along_same_file()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('c', 4);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_cannot_move_to_other_square()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('e', 4);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }
}
