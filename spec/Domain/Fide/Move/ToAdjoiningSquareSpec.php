<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\Move\TooDistant;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class ToAdjoiningSquareSpec extends ObjectBehavior
{
    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination);

        $this->shouldBeAnInstanceOf(BoardMove::class);
    }

    function it_is_in_any_direction_to_adjoining_square()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination);

        $this->inDirection(AlongDiagonal::class)->shouldBe(true);
    }

    function it_is_not_in_direction_not_to_adjoining_square()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination);

        $this->inDirection(LShaped::class)->shouldBe(false);
    }

    function it_moves_piece_from_square_to_adjoining_square_when_limited_to_one_square(Board $board)
    {
        $king = King::forColor(Color::white());

        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination);

        $board->pickPieceFromCoordinates($source)->willReturn($king);
        $board->placePieceAtCoordinates($king, $destination)->shouldBeCalled();

        $this->play($board);
    }

    function it_does_not_allow_move_that_is_further_than_adjoining_square(Board $board)
    {
        $king = King::forColor(Color::white());

        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $this->beConstructedWith($source, $destination);

        $board->pickPieceFromCoordinates($source)->shouldNotBeCalled();
        $board->placePieceAtCoordinates($king, $destination)->shouldNotBeCalled();

        $this->shouldThrow(new TooDistant($this->getWrappedObject()))->during('play', [$board,]);
    }

    function it_is_same_as_other_move_to_adjoining_square()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination);

        $this->is(ToAdjoiningSquare::class)->shouldBe(true);
    }

    function it_is_not_different_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination);

        $this->is(NotIntervened::class)->shouldBe(false);
    }
}
