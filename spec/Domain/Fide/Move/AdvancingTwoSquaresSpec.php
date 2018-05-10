<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Move\TooDistant;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Move\AdvancingTwoSquares;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class AdvancingTwoSquaresSpec extends ObjectBehavior
{
    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination, new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal());

        $this->shouldBeAnInstanceOf(Move::class);
    }

    function it_is_in_any_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 4);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->inDirection(new AlongFile())->shouldBe(true);
    }

    function it_does_not_allow_move_that_is_further_than_two_squares_away()
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 4);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->shouldThrow(new TooDistant($source, $destination, 2))->duringInstantiation();
    }

    function it_moves_piece_from_source_to_destination_while_checking_if_there_is_no_intervening_piece(Board $board)
    {
        $rook = Rook::forColor(Color::white());

        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 4);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $board->pickPieceFromCoordinates($source)->willReturn($rook);
        $board->verifyThatPositionIsUnoccupied(CoordinatePair::fromFileAndRank('a', 3))->willReturn();
        $board->placePieceAtCoordinates($rook, $destination)->shouldBeCalled();

        $this->play($board);
    }

    function it_is_same_as_other_move_limited_to_two_squares_distance()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->is(AdvancingTwoSquares::class)->shouldBe(true);
    }

    function it_is_not_different_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->is(OverOtherPieces::class)->shouldBe(false);
    }
}
