<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class PawnSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_has_color()
    {
        $this->color()->shouldBeLike(Piece\Color::white());
    }

    function it_is_same_as_another_pawn_if_same_color()
    {
        $pawn = Pawn::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_is_not_same_as_another_pawn_if_different_color()
    {
        $pawn = Pawn::forColor(Piece\Color::black());

        $this->isSameAs($pawn)->shouldBe(false);
    }

    function it_is_not_same_as_another_piece_even_if_same_color(Piece $piece)
    {
        $piece->color()->willReturn(Piece\Color::white());

        $this->isSameAs($piece)->shouldBe(false);
    }

    function it_can_move_forward_to_the_square_immediately_in_front_on_the_same_file_for_white()
    {
        $from = Coordinates::fromFileAndRank('d', 3);
        $to = Coordinates::fromFileAndRank('d', 4);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_forward_to_the_square_immediately_in_front_on_the_same_file_for_black()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::black(),]);

        $from = Coordinates::fromFileAndRank('d', 6);
        $to = Coordinates::fromFileAndRank('d', 5);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_cannot_move_backward_to_the_square_immediately_in_front_on_the_same_file_for_white()
    {
        $from = Coordinates::fromFileAndRank('d', 4);
        $to = Coordinates::fromFileAndRank('d', 3);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_cannot_move_backward_to_the_square_immediately_in_front_on_the_same_file_for_black()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::black(),]);

        $from = Coordinates::fromFileAndRank('d', 5);
        $to = Coordinates::fromFileAndRank('d', 6);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_advance_two_squares_along_the_same_file_on_first_move_for_white()
    {
        $from = Coordinates::fromFileAndRank('d', 2);
        $to = Coordinates::fromFileAndRank('d', 4);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_advance_two_squares_along_the_same_file_on_first_move_for_black()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::black(),]);

        $from = Coordinates::fromFileAndRank('d', 7);
        $to = Coordinates::fromFileAndRank('d', 5);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_not_move_two_squares_on_next_moves()
    {
        $this->intentMove(Coordinates::fromFileAndRank('d', 2), Coordinates::fromFileAndRank('d', 3));
        $from = Coordinates::fromFileAndRank('d', 3);
        $to = Coordinates::fromFileAndRank('d', 5);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_along_same_rank_kingside()
    {
        $from = Coordinates::fromFileAndRank('a', 3);
        $to = Coordinates::fromFileAndRank('c', 3);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_along_same_rank_queenside()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('a', 3);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_along_diagonal()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('d', 4);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }
}
