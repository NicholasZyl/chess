<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class PieceWasCapturedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 3));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PieceWasCaptured::class);
    }

    function it_is_event()
    {
        $this->shouldBeAnInstanceOf(Event::class);
    }

    function it_knows_what_piece_was_captured()
    {
        $this->piece()->shouldBeLike(Pawn::forColor(Color::white()));
    }

    function it_knows_which_coordinates_piece_was_captured_at()
    {
        $this->placedAt()->shouldBeLike(CoordinatePair::fromFileAndRank('a', 3));
    }

    function it_is_same_as_other_captured_event()
    {
        $this->equals(new PieceWasCaptured(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 3)))->shouldBe(true);
    }

    function it_is_not_the_same_as_other_event()
    {
        $this->equals(new Event\PieceWasPlacedAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 3)))->shouldBe(false);
    }
}
