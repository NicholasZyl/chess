<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class KnightSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Knight::class);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_knight_if_same_color()
    {
        $pawn = Knight::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_forward_queenside()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('b', 5);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_queenside_forward()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('a', 4);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_forward_kingside()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('d', 5);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_kingside_forward()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('e', 4);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_backward_queenside()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('b', 1);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_queenside_backward()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('a', 2);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_backward_kingside()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('d', 1);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_kingside_backward()
    {
        $from = CoordinatePair::fromFileAndRank('c', 3);
        $to = CoordinatePair::fromFileAndRank('e', 2);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_not_move_to_the_nearest_square_on_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('d', 5);
        $to = CoordinatePair::fromFileAndRank('c', 5);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_further_square_on_same_rank()
    {
        $from = CoordinatePair::fromFileAndRank('d', 5);
        $to = CoordinatePair::fromFileAndRank('g', 5);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_nearest_square_on_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('d', 5);
        $to = CoordinatePair::fromFileAndRank('d', 6);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_further_square_on_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('g', 5);
        $to = CoordinatePair::fromFileAndRank('g', 1);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_nearest_square_on_same_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('d', 5);
        $to = CoordinatePair::fromFileAndRank('c', 6);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_further_square_on_same_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('g', 5);
        $to = CoordinatePair::fromFileAndRank('g', 4);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_further_square_not_on_same_rank_file_or_diagonal()
    {
        $from = CoordinatePair::fromFileAndRank('g', 5);
        $to = CoordinatePair::fromFileAndRank('c', 2);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }
}
