<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\MoveToUnoccupiedPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Move\Capturing;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class CapturingSpec extends ObjectBehavior
{
    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new AlongFile();
        $move = new NotIntervened($source, $destination, $direction);
        $this->beConstructedWith($move);

        $this->shouldBeAnInstanceOf(BoardMove::class);
    }

    function it_can_capture_opponents_piece_at_destination(Board $board)
    {
        $bishop = Bishop::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongDiagonal();
        $move = new NotIntervened($source, $destination, $direction);
        $this->beConstructedWith($move);

        $board->peekPieceAtCoordinates($source)->willReturn($bishop);
        $board->hasOpponentsPieceAt($destination, Color::white())->willReturn(true);
        $board->pickPieceFromCoordinates($source)->willReturn($bishop);
        $board->placePieceAtCoordinates($bishop, $destination)->shouldBeCalled();

        $this->play($board);
    }

    function it_cannot_be_made_to_square_with_no_opponents_piece_at_destination(Board $board)
    {
        $bishop = Bishop::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongDiagonal();
        $move = new NotIntervened($source, $destination, $direction);
        $this->beConstructedWith($move);

        $board->peekPieceAtCoordinates($source)->willReturn($bishop);
        $board->hasOpponentsPieceAt($destination, Color::white())->willReturn(false);
        $board->pickPieceFromCoordinates($source)->shouldNotBeCalled();
        $board->placePieceAtCoordinates($bishop, $source)->shouldNotBeCalled();

        $this->shouldThrow(new MoveToUnoccupiedPosition($destination))->during('play', [$board,]);
    }

    function it_is_same_as_base_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongDiagonal();
        $move = new NotIntervened($source, $destination, $direction);
        $this->beConstructedWith($move);

        $this->is(NotIntervened::class)->shouldBe(true);
    }

    function it_capturing()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongDiagonal();
        $move = new NotIntervened($source, $destination, $direction);
        $this->beConstructedWith($move);

        $this->is(Capturing::class)->shouldBe(true);
    }

    function it_is_not_different_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongDiagonal();
        $move = new NotIntervened($source, $destination, $direction);
        $this->beConstructedWith($move);

        $this->is(OverOtherPieces::class)->shouldBe(false);
    }
}
