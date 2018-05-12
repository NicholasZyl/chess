<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalMove;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use PhpSpec\ObjectBehavior;

class MoveTooDistantSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            CoordinatePair::fromFileAndRank('a', 3),
            CoordinatePair::fromFileAndRank('a', 5),
            1
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IllegalMove\MoveTooDistant::class);
    }

    function it_is_illegal_move()
    {
        $this->shouldBeAnInstanceOf(IllegalMove::class);
    }

    function it_specifies_the_reason_why_it_is_illegal()
    {
        $this->getMessage()->shouldContain('is further than 1 square away');
    }
}
