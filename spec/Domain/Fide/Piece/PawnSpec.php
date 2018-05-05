<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Chessboard\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Chessboard\Exception\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongFile;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongRank;
use NicholasZyl\Chess\Domain\Chessboard\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class PawnSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_a_chess_piece()
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

    function it_can_move_forward_to_the_square_immediately_in_front_on_the_same_file(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 2);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->mayMove($move, $board);
    }

    function it_cannot_move_along_rank(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $move = AlongRank::between(
            $from,
            $to
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($move, $this->getWrappedObject()))->during('mayMove', [$move, $board,]);
    }

    function it_cannot_move_along_diagonal(Board $board)
    {
        $move = AlongDiagonal::between(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($move, $this->getWrappedObject()))->during('mayMove', [$move, $board,]);
    }

    function it_cannot_move_to_nearest_square_not_on_same_rank_file_or_diagonal(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('c', 2);
        $move = NearestNotSameFileRankOrDiagonal::between(
            $from,
            $to
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($move, $this->getWrappedObject()))->during('mayMove', [$move, $board,]);
    }

    function it_cannot_move_backward_to_the_square_immediately_in_front_on_the_same_file_for_white(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('a', 1);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($move, $this->getWrappedObject()))->during('mayMove', [$move, $board,]);
    }

    function it_cannot_move_backward_to_the_square_immediately_in_front_on_the_same_file_for_black(Board $board)
    {
        $this->beConstructedThrough('forColor', [Piece\Color::black(),]);

        $from = CoordinatePair::fromFileAndRank('a', 7);
        $to = CoordinatePair::fromFileAndRank('a', 8);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($move, $this->getWrappedObject()))->during('mayMove', [$move, $board,]);
    }

    function it_can_advance_two_squares_along_the_same_file_on_first_move_for_white(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 2);
        $to = CoordinatePair::fromFileAndRank('a', 4);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->mayMove($move, $board);
    }

    function it_can_advance_two_squares_along_the_same_file_on_first_move_for_black(Board $board)
    {
        $this->beConstructedThrough('forColor', [Piece\Color::black(),]);

        $from = CoordinatePair::fromFileAndRank('a', 7);
        $to = CoordinatePair::fromFileAndRank('a', 5);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->mayMove($move, $board);
    }

    function it_cannot_move_more_than_to_the_square_immediately_in_front_on_the_same_file_on_next_moves(Board $board)
    {
        $firstMove = AlongFile::between(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 3)
        );
        $this->mayMove($firstMove, $board);

        $from = CoordinatePair::fromFileAndRank('a', 3);
        $to = CoordinatePair::fromFileAndRank('a', 5);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($move, $this->getWrappedObject()))->during('mayMove', [$move, $board,]);
    }

    function it_cannot_advance_two_squares_if_any_is_occupied(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 3);
        $move = AlongFile::between(
            $from,
            $to
        );

        $interveningPosition = CoordinatePair::fromFileAndRank('a', 2);
        $board->verifyThatPositionIsUnoccupied($interveningPosition)->willThrow(new SquareIsOccupied($interveningPosition));

        $this->shouldThrow(new MoveOverInterveningPiece($move, $interveningPosition))->during('mayMove', [$move, $board,]);
    }
}
