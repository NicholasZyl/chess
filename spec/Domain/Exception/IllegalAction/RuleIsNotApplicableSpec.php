<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use PhpSpec\ObjectBehavior;

class RuleIsNotApplicableSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RuleIsNotApplicable::class);
    }

    function it_is_illegal_action()
    {
        $this->shouldBeAnInstanceOf(IllegalAction::class);
    }
}
