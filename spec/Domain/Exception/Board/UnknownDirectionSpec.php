<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Exception\Board\UnknownDirection;
use PhpSpec\ObjectBehavior;

class UnknownDirectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            CoordinatePair::fromFileAndRank('a', 3),
            CoordinatePair::fromFileAndRank('e', 8)
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UnknownDirection::class);
    }

    function it_specifies_coordinates_of_the_move()
    {
        $this->getMessage()->shouldContain('a3');
        $this->getMessage()->shouldContain('e8');
    }
}
