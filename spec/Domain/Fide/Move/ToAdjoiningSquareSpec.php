<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Move\TooDistant;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class ToAdjoiningSquareSpec extends ObjectBehavior
{
    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->shouldBeAnInstanceOf(Move::class);
    }

    function it_is_in_any_direction_to_adjoining_square()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->inDirection(new AlongFile())->shouldBe(true);
    }

    function it_is_not_in_direction_not_to_adjoining_square()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->inDirection(new LShaped())->shouldBe(false);
    }

    function it_moves_piece_from_square_to_adjoining_square_when_limited_to_one_square(Board $board)
    {
        $king = King::forColor(Color::white());

        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $board->pickPieceFromCoordinates($source)->willReturn($king);
        $board->placePieceAtCoordinates($king, $destination)->shouldBeCalled();

        $this->play($board);
    }

    function it_does_not_allow_move_that_is_further_than_adjoining_square()
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('c', 2);
        $this->beConstructedWith($source, $destination, new LShaped());

        $this->shouldThrow(new TooDistant($source, $destination, 1))->duringInstantiation();
    }

    function it_is_same_as_other_move_to_adjoining_square()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->is(ToAdjoiningSquare::class)->shouldBe(true);
    }

    function it_is_not_different_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->is(NotIntervened::class)->shouldBe(false);
    }
}
