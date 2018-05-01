<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Exception;

use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Exception\IncompleteRules;
use PhpSpec\ObjectBehavior;

class IncompleteRulesSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([Rank::king(), Rank::queen(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IncompleteRules::class);
    }

    function it_is_runtime_exception()
    {
        $this->shouldBeAnInstanceOf(\RuntimeException::class);
    }

    function it_lists_all_missing_rules()
    {
        $this->getMessage()->shouldContain('king, queen');
    }
}
