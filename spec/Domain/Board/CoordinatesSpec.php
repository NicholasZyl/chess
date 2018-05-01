<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use PhpSpec\ObjectBehavior;

class CoordinatesSpec extends ObjectBehavior
{
    public function it_is_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['A1']);
        $this->shouldHaveType(Coordinates::class);
    }
}
