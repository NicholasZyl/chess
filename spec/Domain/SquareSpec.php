<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use PhpSpec\ObjectBehavior;

class SquareSpec extends ObjectBehavior
{
    /** @var Coordinates */
    private $coordinates;

    function let()
    {
        $this->coordinates = CoordinatePair::fromString('a1');
        $this->beConstructedThrough('forCoordinates', [$this->coordinates]);
    }

    function it_has_coordinates()
    {
        $this->coordinates()->shouldBe($this->coordinates);
    }

    function it_allows_to_place_piece_on_it()
    {
        $piece = Pawn::forColor(Color::white());
        $this->place($piece)->shouldBe(null);
    }

    function it_allows_to_pick_piece_placed_on_it()
    {
        $piece = Pawn::forColor(Color::white());
        $this->place($piece);
        $this->pick()->shouldBe($piece);
    }

    function it_does_not_allow_to_pick_a_piece_if_none_is_placed()
    {
        $this->shouldThrow(new SquareIsUnoccupied($this->coordinates))->during('pick');
    }

    function it_is_vacant_after_piece_is_picked()
    {
        $piece = Pawn::forColor(Color::white());
        $this->place($piece);
        $this->pick();

        $this->shouldThrow(new SquareIsUnoccupied($this->coordinates))->during('pick');
    }

    function it_knows_when_is_occupied()
    {
        $piece = Pawn::forColor(Color::white());
        $this->place($piece);

        $this->isOccupied()->shouldBe(true);
    }

    function it_knows_when_is_unoccupied()
    {
        $this->isOccupied()->shouldBe(false);
    }

    function it_returns_exchanged_piece_when_placing_another_piece_on_it()
    {
        $previousPiece = Pawn::forColor(Color::black());
        $this->place($previousPiece);

        $piece = Knight::forColor(Color::white());

        $this->place($piece)->shouldBeLike($previousPiece);
    }

    function it_knows_that_it_has_placed_piece_in_given_color_on_it()
    {
        $piece = Pawn::forColor(Color::white());
        $this->place($piece);

        $this->isOccupiedBy(Color::white())->shouldBe(true);
    }

    function it_knows_that_it_has_not_placed_piece_in_given_color_when_piece_has_different_color()
    {
        $piece = Pawn::forColor(Color::white());
        $this->place($piece);

        $this->isOccupiedBy(Color::black())->shouldBe(false);
    }

    function it_knows_that_it_has_not_placed_piece_in_given_color_when_is_unoccupied()
    {
        $this->isOccupiedBy(Color::black())->shouldBe(false);
    }
}
