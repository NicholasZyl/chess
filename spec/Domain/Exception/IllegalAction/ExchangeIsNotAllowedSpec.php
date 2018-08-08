<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use PhpSpec\ObjectBehavior;

class ExchangeIsNotAllowedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(CoordinatePair::fromFileAndRank('a', 1));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ExchangeIsNotAllowed::class);
    }

    function it_is_illegal_action()
    {
        $this->shouldBeAnInstanceOf(IllegalAction::class);
    }

    function it_knows_on_which_position_exchange_was_to_happen()
    {
        $this->position()->shouldBeLike(CoordinatePair::fromFileAndRank('a', 1));
    }
}
