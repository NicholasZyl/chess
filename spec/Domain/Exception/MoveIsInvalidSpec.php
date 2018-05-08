<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\MoveIsInvalid;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use PhpSpec\ObjectBehavior;

class MoveIsInvalidSpec extends ObjectBehavior
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
        $this->shouldHaveType(MoveIsInvalid::class);
    }

    function it_is_illegal_move()
    {
        $this->shouldBeAnInstanceOf(IllegalMove::class);
    }

    function it_specifies_coordinates_of_the_move()
    {
        $this->getMessage()->shouldContain('a3');
        $this->getMessage()->shouldContain('e8');
    }

    function it_specifies_the_reason_why_it_is_illegal()
    {
        $this->getMessage()->shouldContain('is not possible');
    }
}
