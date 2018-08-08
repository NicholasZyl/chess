<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\GameEnded;
use PhpSpec\ObjectBehavior;

class GameEndedSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(GameEnded::class);
    }

    function it_is_event()
    {
        $this->shouldBeAnInstanceOf(Event::class);
    }

    function it_knows_which_player_won()
    {
        $this->beConstructedWith(Color::white());

        $this->winner()->shouldBeLike(Color::white());
    }

    function it_is_a_drawn_if_no_player_won()
    {
        $this->winner()->shouldBe(null);
    }

    function it_is_equal_to_another_event_with_the_same_winner()
    {
        $this->beConstructedWith(Color::white());

        $anotherEvent = new GameEnded(Color::white());

        $this->equals($anotherEvent)->shouldBe(true);
    }

    function it_is_not_equal_to_another_event_if_not_the_same_winner()
    {
        $this->beConstructedWith(Color::white());

        $anotherEvent = new GameEnded();

        $this->equals($anotherEvent)->shouldBe(false);

    }

    function it_is_not_equal_to_different_event()
    {
        $anotherEvent = new Event\Checkmated(Color::white());

        $this->equals($anotherEvent)->shouldBe(false);
    }
}
