<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Color;
use PhpSpec\ObjectBehavior;

class ColorSpec extends ObjectBehavior
{
    public function it_can_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['white']);
        $this->shouldBeAnInstanceOf(Color::class);
    }

    public function it_is_the_same_as_second_color()
    {
        $this->beConstructedThrough('fromString', ['white']);
        $anotherColor = Color::fromString('white');

        $this->isSameAs($anotherColor)->shouldBe(true);
    }

    public function it_is_different_when_comparing_with_another_color()
    {
        $this->beConstructedThrough('fromString', ['white']);
        $anotherColor = Color::fromString('black');

        $this->isSameAs($anotherColor)->shouldBe(false);
    }
}
