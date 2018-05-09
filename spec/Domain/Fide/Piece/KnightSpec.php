<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Move\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\Capturing;
use NicholasZyl\Chess\Domain\Fide\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Fide\Move\ToUnoccupiedSquare;
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

    function it_may_move_to_one_of_the_squares_nearest_to_that_on_which_it_stands_but_not_on_same_rank_file_or_diagonal()
    {
        $move = new ToUnoccupiedSquare(
            new OverOtherPieces(
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('b', 3),
                new LShaped()
            )
        );

        $this->canMove($move);
    }

    function it_may_not_move_to_adjoining_square()
    {
        $move = new ToUnoccupiedSquare(
            new ToAdjoiningSquare(
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('b', 2)
            )
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_not_move_along_file()
    {
        $move = new ToUnoccupiedSquare(
            new OverOtherPieces(
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('a', 5),
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
            )
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_not_move_along_rank()
    {
        $move = new ToUnoccupiedSquare(
            new NotIntervened(
                CoordinatePair::fromFileAndRank('a', 5),
                CoordinatePair::fromFileAndRank('f', 5),
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
            )
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_not_move_along_diagonal()
    {
        $move = new ToUnoccupiedSquare(
            new NotIntervened(
                CoordinatePair::fromFileAndRank('h', 8),
                CoordinatePair::fromFileAndRank('a', 1),
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
            )
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_capture_the_same_way_it_moves()
    {
        $move = new Capturing(
            new OverOtherPieces(
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('b', 3),
                new LShaped()
            )
        );

        $this->canMove($move);
    }





    function it_can_move_to_nearest_square_not_on_same_rank_file_or_diagonal(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('c', 2);
        $move = NearestNotSameFileRankOrDiagonal::between(
            $from,
            $to
        );

        $this->mayMove($move, $board);
    }

    function it_cannot_move_along_diagonal(Board $board)
    {
        $move = AlongDiagonal::between(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($move, $this->getWrappedObject()))->during('mayMove', [$move, $board,]);
    }

    function it_cannot_move_along_file(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 2);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($move, $this->getWrappedObject()))->during('mayMove', [$move, $board,]);
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
}
