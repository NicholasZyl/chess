<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Exception;

use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Exception\MissingRule;
use PhpSpec\ObjectBehavior;

class MissingRuleSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Rank::queen());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MissingRule::class);
    }

    function it_is_runtime_exception()
    {
        $this->shouldBeAnInstanceOf(\RuntimeException::class);
    }

    function it_describes_for_which_rank_rule_is_missing()
    {
        $this->getMessage()->shouldContain('queen');
    }
}
