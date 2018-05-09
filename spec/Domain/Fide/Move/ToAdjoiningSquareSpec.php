<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\Move\TooDistant;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class ToAdjoiningSquareSpec extends ObjectBehavior
{
    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new AlongFile();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldBeAnInstanceOf(BoardMove::class);
    }

    function it_moves_piece_from_square_to_adjoining_square_when_limited_to_one_square(Board $board)
    {
        $king = King::forColor(Color::white());

        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new AlongFile();
        $this->beConstructedWith($source, $destination, $direction);

        $board->pickPieceFromCoordinates($source)->willReturn($king);
        $board->placePieceAtCoordinates($king, $destination)->shouldBeCalled();

        $this->play($board);
    }

    function it_does_not_allow_move_that_is_further_than_adjoining_square(Board $board)
    {
        $king = King::forColor(Color::white());

        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $direction = new AlongDiagonal();
        $this->beConstructedWith($source, $destination, $direction);

        $board->pickPieceFromCoordinates($source)->shouldNotBeCalled();
        $board->placePieceAtCoordinates($king, $destination)->shouldNotBeCalled();

        $this->shouldThrow(new TooDistant($this->getWrappedObject()))->during('play', [$board,]);
    }
}
