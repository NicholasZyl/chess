<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use PhpSpec\ObjectBehavior;

class CoordinatesNotReachableSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            CoordinatePair::fromFileAndRank('a', 3),
            CoordinatePair::fromFileAndRank('e', 3),
            new AlongFile()
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CoordinatesNotReachable::class);
    }

    function it_specifies_which_coordinates_are_not_reachable_with_given_direction()
    {
        $this->getMessage()->shouldContain('a3');
        $this->getMessage()->shouldContain('e3');
        $this->getMessage()->shouldContain('along same file');
    }
}
