<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
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
}
