<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Board\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Exception\SquareIsVacant;
use PhpSpec\ObjectBehavior;

class SquareIsVacantSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Coordinates::fromString('A1'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SquareIsVacant::class);
    }

    public function it_is_runtime_exception()
    {
        $this->shouldBeAnInstanceOf(\RuntimeException::class);
    }

    public function it_describes_coordinates_of_vacant_square()
    {
        $this->getMessage()->shouldContain('A1');
    }
}
