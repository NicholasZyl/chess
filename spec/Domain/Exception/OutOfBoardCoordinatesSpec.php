<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\OutOfBoardCoordinates;
use PhpSpec\ObjectBehavior;

class OutOfBoardCoordinatesSpec extends ObjectBehavior
{
    function let(Coordinates $coordinates)
    {
        $coordinates->__toString()->willReturn('00');
        $this->beConstructedWith($coordinates);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OutOfBoardCoordinates::class);
    }

    function it_is_invalid_position_exception()
    {
        $this->shouldBeAnInstanceOf(\RuntimeException::class);
    }

    function it_specifies_position_coordinates()
    {
        $this->getMessage()->shouldContain('00');
    }
}
