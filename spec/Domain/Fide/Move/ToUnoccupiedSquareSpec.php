<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Fide\Move\ToUnoccupiedSquare;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class ToUnoccupiedSquareSpec extends ObjectBehavior
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

    function it_does_not_allow_move_to_occupied_square(Board $board)
    {
        $knight = Knight::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('c', 2);
        $move = new OverOtherPieces($source, $destination, new LShaped());
        $this->beConstructedWith($move);

        $board->verifyThatPositionIsUnoccupied($destination)->willThrow(new SquareIsOccupied($destination));
        $board->pickPieceFromCoordinates($source)->shouldNotBeCalled();
        $board->placePieceAtCoordinates($knight, $destination)->shouldNotBeCalled();

        $this->shouldThrow(new MoveToOccupiedPosition($destination))->during('play', [$board,]);
    }

    function it_does_allows_move_to_unoccupied_square(Board $board)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $move = new ToAdjoiningSquare($source, $destination, new Forward(Color::white(), new AlongFile()));
        $this->beConstructedWith($move);

        $board->verifyThatPositionIsUnoccupied($destination)->willReturn();
        $board->pickPieceFromCoordinates($source)->shouldBeCalled()->willReturn($pawn);
        $board->placePieceAtCoordinates($pawn, $destination)->shouldBeCalled();

        $this->play($board);
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

    function it_move_to_unoccupied_square()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongDiagonal();
        $move = new NotIntervened($source, $destination, $direction);
        $this->beConstructedWith($move);

        $this->is(ToUnoccupiedSquare::class)->shouldBe(true);
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

    function it_is_in_direction_of_base_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongDiagonal();
        $move = new NotIntervened($source, $destination, $direction);
        $this->beConstructedWith($move);

        $this->inDirection(new AlongDiagonal())->shouldBe(true);
    }
}
