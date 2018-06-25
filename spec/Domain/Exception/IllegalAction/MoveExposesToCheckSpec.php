<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveExposesToCheck;
use PhpSpec\ObjectBehavior;

class MoveExposesToCheckSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MoveExposesToCheck::class);
    }

    function it_is_illegal_action()
    {
        $this->shouldBeAnInstanceOf(IllegalAction::class);
    }
}
