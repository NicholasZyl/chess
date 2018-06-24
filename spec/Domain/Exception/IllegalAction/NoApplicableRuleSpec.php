<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;
use PhpSpec\ObjectBehavior;

class NoApplicableRuleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NoApplicableRule::class);
    }

    function it_is_illegal_action()
    {
        $this->shouldBeAnInstanceOf(IllegalAction::class);
    }
}
