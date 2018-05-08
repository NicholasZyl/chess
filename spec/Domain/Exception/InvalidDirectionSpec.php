<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use PhpSpec\ObjectBehavior;

class InvalidDirectionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            CoordinatePair::fromFileAndRank('a', 3),
            CoordinatePair::fromFileAndRank('e', 8),
            'along same rank'
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InvalidDirection::class);
    }

    function it_specifies_coordinates_of_the_move()
    {
        $this->getMessage()->shouldContain('a3');
        $this->getMessage()->shouldContain('e8');
    }
}
