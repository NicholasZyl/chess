<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ActionNotAllowed;
use PhpSpec\ObjectBehavior;

class ActionNotAllowedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('exemplar reason');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ActionNotAllowed::class);
    }

    function it_is_illegal_action()
    {
        $this->shouldBeAnInstanceOf(IllegalAction::class);
    }
}
