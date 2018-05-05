<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece;
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

    function it_allows_to_place_piece_on_it()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);
    }

    function it_allows_to_pick_piece_placed_on_it()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);
        $this->pick()->shouldBe($piece);
    }

    function it_does_not_allow_to_pick_a_piece_if_none_is_placed()
    {
        $this->shouldThrow(new SquareIsUnoccupied($this->coordinates))->during('pick');
    }

    function it_is_vacant_after_piece_is_picked()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);
        $this->pick();

        $this->shouldThrow(new SquareIsUnoccupied($this->coordinates))->during('pick');
    }

    function it_allows_to_check_what_piece_is_placed_on_it()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);

        $this->hasPlacedPiece($piece)->shouldBe(true);
    }

    function it_disallows_placing_piece_on_square_occupied_with_same_color()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);

        $movingPiece = Knight::forColor(Piece\Color::white());

        $this->shouldThrow(new SquareIsOccupied($this->coordinates))->during('place', [$movingPiece,]);
    }

    function it_can_be_verified_as_unoccupied()
    {
        $this->verifyThatUnoccupied();
    }

    function it_can_not_be_verified_as_unoccupied_when_piece_is_placed()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);

        $this->shouldThrow(new SquareIsOccupied($this->coordinates))->during('verifyThatUnoccupied');
    }
}
