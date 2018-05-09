<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OverOtherPiecesSpec extends ObjectBehavior
{
    function let(Board $board)
    {
        $board->verifyThatPositionIsUnoccupied(Argument::cetera())->willReturn();
    }

    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new LShaped();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldBeAnInstanceOf(BoardMove::class);
    }

    function it_validates_if_coordinates_are_on_same_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new LShaped();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldThrow(new InvalidDirection($source, $destination, $direction))->duringInstantiation();
    }

    function it_knows_source_destination_and_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $direction = new LShaped();
        $this->beConstructedWith($source, $destination, $direction);

        $this->source()->shouldBe($source);
        $this->destination()->shouldBe($destination);
        $this->direction()->shouldBe($direction);
    }

    function it_moves_piece_from_one_square_to_another(Board $board)
    {
        $knight = Knight::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $this->beConstructedWith($source, $destination, new LShaped());

        $board->pickPieceFromCoordinates($source)->willReturn($knight);
        $board->placePieceAtCoordinates($knight, $destination)->shouldBeCalled();

        $this->play($board);
    }
}