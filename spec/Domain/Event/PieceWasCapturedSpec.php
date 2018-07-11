<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
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
        $this->capturedAt()->shouldBeLike(CoordinatePair::fromFileAndRank('a', 3));
    }

    function it_equals_another_event_if_for_capturing_same_piece_on_same_position()
    {
        $another = new PieceWasCaptured(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 3));

        $this->equals($another)->shouldBe(true);
    }

    function it_does_not_equal_another_event_if_for_capturing_other_piece_on_same_position()
    {
        $another = new PieceWasCaptured(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('a', 3));

        $this->equals($another)->shouldBe(false);
    }

    function it_does_not_equal_another_event()
    {
        $another = new Event\PieceWasExchanged(Pawn::forColor(Color::white()), Queen::forColor(Color::black()), CoordinatePair::fromFileAndRank('a', 3));

        $this->equals($another)->shouldBe(false);
    }
}
