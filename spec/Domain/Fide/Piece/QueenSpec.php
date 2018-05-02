<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class QueenSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Queen::class);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_queen_if_same_color()
    {
        $pawn = Queen::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_can_move_to_any_square_along_file_forward()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c8');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_file_backward()
    {
        $from = Coordinates::fromString('c6');
        $to = Coordinates::fromString('c1');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_rank_to_queenside()
    {
        $from = Coordinates::fromString('c4');
        $to = Coordinates::fromString('c2');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_rank_to_kingside()
    {
        $from = Coordinates::fromString('c4');
        $to = Coordinates::fromString('c7');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_diagonal_forward_kingside()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('f6');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_diagonal_forward_queenside()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('a7');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_diagonal_backward_kingside()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('e3');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_any_square_along_diagonal_backward_queenside()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('b2');

        $this->intentMove($from, $to);
    }

    function it_cannot_move_to_other_square()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('c2');

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }
}
