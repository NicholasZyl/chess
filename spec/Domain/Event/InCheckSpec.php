<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\InCheck;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class InCheckSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            King::forColor(Color::white())
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InCheck::class);
    }

    function it_is_event()
    {
        $this->shouldBeAnInstanceOf(Event::class);
    }

    function it_knows_which_piece_is_in_check()
    {
        $this->piece()->shouldBeLike(King::forColor(Color::white()));
    }

    function it_equals_another_event_if_check_for_the_same_piece()
    {
        $another = new InCheck(King::forColor(Color::white()));

        $this->equals($another)->shouldBe(true);
    }

    function it_does_not_equal_another_event_if_for_capturing_other_piece_on_same_position()
    {
        $another = new InCheck(King::forColor(Color::black()));

        $this->equals($another)->shouldBe(false);
    }

    function it_does_not_equal_another_event()
    {
        $another = new Event\PieceWasExchanged(Pawn::forColor(Color::white()), Queen::forColor(Color::black()), CoordinatePair::fromFileAndRank('a', 3));

        $this->equals($another)->shouldBe(false);
    }
}