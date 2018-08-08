<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\Stalemate;
use PhpSpec\ObjectBehavior;

class StalemateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Stalemate::class);
    }

    function it_is_event()
    {
        $this->shouldBeAnInstanceOf(Event::class);
    }

    function it_is_equal_to_another_stalemate_event()
    {
        $anotherStaleMate = new Stalemate();

        $this->equals($anotherStaleMate)->shouldBe(true);
    }

    function it_is_not_equal_to_different_event()
    {
        $anotherEvent = new Event\Checkmated(Color::white());

        $this->equals($anotherEvent)->shouldBe(false);
    }
}
