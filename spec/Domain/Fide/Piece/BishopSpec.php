<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Move\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\Capturing;
use NicholasZyl\Chess\Domain\Fide\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Move\ToUnoccupiedSquare;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class BishopSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
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

    function it_may_move_to_any_square_along_a_diagonal_on_which_it_stands()
    {
        $move = new ToUnoccupiedSquare(
            new NotIntervened(
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('b', 2),
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
            )
        );

        $this->canMove($move);
    }

    function it_may_not_move_over_any_intervening_pieces()
    {
        $move = new ToUnoccupiedSquare(
            new OverOtherPieces(
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('b', 2),
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
            )
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_not_move_along_file()
    {
        $move = new ToUnoccupiedSquare(
            new NotIntervened(
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('a', 2),
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
            )
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_not_move_along_rank()
    {
        $move = new ToUnoccupiedSquare(
            new NotIntervened(
                CoordinatePair::fromFileAndRank('a', 2),
                CoordinatePair::fromFileAndRank('b', 2),
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
            )
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_not_move_to_nearest_square()
    {
        $move = new ToUnoccupiedSquare(
            new NotIntervened(
                CoordinatePair::fromFileAndRank('a', 2),
                CoordinatePair::fromFileAndRank('c', 1),
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped()
            )
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_capture_at_any_square_along_a_diagonal_on_which_it_stands()
    {
        $move = new Capturing(
            new NotIntervened(
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('b', 2),
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
            )
        );

        $this->canMove($move);
    }





    function it_can_move_along_diagonal(Board $board)
    {
        $move = AlongDiagonal::between(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->mayMove($move, $board);
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

    function it_cannot_move_to_nearest_square(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('c', 2);
        $move = NearestNotSameFileRankOrDiagonal::between(
            $from,
            $to
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($move, $this->getWrappedObject()))->during('mayMove', [$move, $board,]);
    }

    function it_cannot_move_over_intervening_pieces(Board $board)
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('c', 3);
        $move = AlongDiagonal::between(
            $from,
            $to
        );

        $interveningPosition = CoordinatePair::fromFileAndRank('b', 2);
        $board->verifyThatPositionIsUnoccupied($interveningPosition)->willThrow(new SquareIsOccupied($interveningPosition));

        $this->shouldThrow(new MoveOverInterveningPiece($interveningPosition))->during('mayMove', [$move, $board,]);
    }
}
