<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\InCheck;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Queen;
use PhpSpec\ObjectBehavior;

class InCheckSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            Color::white()
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

    function it_knows_which_color_is_in_check()
    {
        $this->color()->shouldBeLike(Color::white());
    }

    function it_equals_another_event_if_check_for_the_same_color()
    {
        $another = new InCheck(Color::white());

        $this->equals($another)->shouldBe(true);
    }

    function it_does_not_equal_another_event_if_check_is_for_another_color()
    {
        $another = new InCheck(Color::black());

        $this->equals($another)->shouldBe(false);
    }

    function it_does_not_equal_another_event()
    {
        $another = new Event\PieceWasExchanged(Pawn::forColor(Color::white()), Queen::forColor(Color::black()), CoordinatePair::fromFileAndRank('a', 3));

        $this->equals($another)->shouldBe(false);
    }
}
