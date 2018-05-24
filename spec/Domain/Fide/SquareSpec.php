<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event\PieceWasCapturedAt;
use NicholasZyl\Chess\Domain\Event\PieceWasPlacedAt;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
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

    function it_has_coordinates()
    {
        $this->coordinates()->shouldBe($this->coordinates);
    }

    function it_allows_to_place_piece_on_it()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece)->shouldBeLike([new PieceWasPlacedAt($piece, $this->coordinates),]);
    }

    function it_allows_to_peek_at_it_to_see_what_piece_is_placed()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);
        $this->peek()->shouldBe($piece);
        $this->hasPlacedPiece($piece)->shouldBe(true);
    }

    function it_knows_when_peeking_at_unoccupied_square()
    {
        $this->shouldThrow(new SquareIsUnoccupied($this->coordinates))->during('peek');
    }

    function it_notifies_piece_that_it_was_placed(Piece $piece)
    {
        $piece->color()->willReturn(Piece\Color::white());
        $piece->placeAt($this->coordinates)->shouldBeCalled();

        $this->place($piece);
    }

    function it_allows_to_pick_piece_placed_on_it()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);
        $this->pick()->shouldBe($piece);
        $this->hasPlacedPiece($piece)->shouldBe(false);
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

    function it_allows_to_check_if_given_piece_is_placed_on_it()
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

    function it_captures_piece_when_placing_piece_with_another_color()
    {
        $opponentPiece = Pawn::forColor(Piece\Color::black());
        $this->place($opponentPiece);

        $piece = Knight::forColor(Piece\Color::white());

        $this->place($piece)->shouldBeLike([new PieceWasCapturedAt($opponentPiece, $this->coordinates), new PieceWasPlacedAt($piece, $this->coordinates),]);
        $this->hasPlacedPiece($piece)->shouldBe(true);
    }

    function it_knows_that_it_has_placed_opponents_piece_on_it()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);

        $this->hasPlacedOpponentsPiece(Piece\Color::black())->shouldBe(true);
    }

    function it_knows_that_it_has_not_placed_opponents_piece_when_piece_has_different_color()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $this->place($piece);

        $this->hasPlacedOpponentsPiece(Piece\Color::white())->shouldBe(false);
    }

    function it_knows_that_it_has_not_placed_opponents_piece_when_is_unoccupied()
    {
        $this->hasPlacedOpponentsPiece(Piece\Color::black())->shouldBe(false);
    }
}
