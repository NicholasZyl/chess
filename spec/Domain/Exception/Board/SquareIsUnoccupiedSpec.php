<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use PhpSpec\ObjectBehavior;

class SquareIsUnoccupiedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(CoordinatePair::fromString('A1'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SquareIsUnoccupied::class);
    }

    function it_describes_coordinates_of_vacant_square()
    {
        $this->getMessage()->shouldContain('a1');
    }
}
