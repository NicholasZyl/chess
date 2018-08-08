<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
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

    function it_knows_coordinates_of_square()
    {
        $this->coordinates()->shouldBeLike(CoordinatePair::fromString('a1'));
    }
}
