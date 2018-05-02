<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
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

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_forward_queenside()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('b', 5);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_queenside_forward()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('a', 4);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_forward_kingside()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('d', 5);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_kingside_forward()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('e', 4);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_backward_queenside()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('b', 1);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_queenside_backward()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('a', 2);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_backward_kingside()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('d', 1);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_move_to_the_nearest_square_not_on_same_rank_file_or_diagonal_kingside_backward()
    {
        $from = Coordinates::fromFileAndRank('c', 3);
        $to = Coordinates::fromFileAndRank('e', 2);

        $this->intentMove($from, $to)->shouldBeLike(Move::between($from, $to));
    }

    function it_can_not_move_to_the_nearest_square_on_same_rank()
    {
        $from = Coordinates::fromFileAndRank('d', 5);
        $to = Coordinates::fromFileAndRank('c', 5);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_further_square_on_same_rank()
    {
        $from = Coordinates::fromFileAndRank('d', 5);
        $to = Coordinates::fromFileAndRank('g', 5);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_nearest_square_on_same_file()
    {
        $from = Coordinates::fromFileAndRank('d', 5);
        $to = Coordinates::fromFileAndRank('d', 6);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_further_square_on_same_file()
    {
        $from = Coordinates::fromFileAndRank('g', 5);
        $to = Coordinates::fromFileAndRank('g', 1);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_nearest_square_on_same_diagonal()
    {
        $from = Coordinates::fromFileAndRank('d', 5);
        $to = Coordinates::fromFileAndRank('c', 6);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_the_further_square_on_same_diagonal()
    {
        $from = Coordinates::fromFileAndRank('g', 5);
        $to = Coordinates::fromFileAndRank('g', 4);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_can_not_move_to_further_square_not_on_same_rank_file_or_diagonal()
    {
        $from = Coordinates::fromFileAndRank('g', 5);
        $to = Coordinates::fromFileAndRank('c', 2);

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }
}
