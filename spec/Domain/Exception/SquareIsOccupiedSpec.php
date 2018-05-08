<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use PhpSpec\ObjectBehavior;

class SquareIsOccupiedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(CoordinatePair::fromString('a1'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SquareIsOccupied::class);
    }

    function it_describes_coordinates_of_vacant_square()
    {
        $this->getMessage()->shouldContain('a1');
    }
}
