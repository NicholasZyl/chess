<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalMove;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MovePrevented;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use PhpSpec\ObjectBehavior;

class MovePreventedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            new NotIntervened(
                CoordinatePair::fromFileAndRank('a', 2),
                CoordinatePair::fromFileAndRank('b', 2),
                new AlongRank()
            )
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MovePrevented::class);
    }

    function it_is_illegal_move()
    {
        $this->shouldBeAnInstanceOf(IllegalMove::class);
    }

    function it_specifies_the_reason_why_it_is_illegal()
    {
        $this->getMessage()->shouldContain('is prevented');
    }
}
