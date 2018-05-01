<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Color;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ColorSpec extends ObjectBehavior
{
    public function it_can_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['white']);
        $this->shouldBeAnInstanceOf(Color::class);
    }
}
