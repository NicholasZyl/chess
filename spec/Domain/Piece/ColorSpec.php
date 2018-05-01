<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class ColorSpec extends ObjectBehavior
{
    function it_can_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['white']);
        $this->shouldBeAnInstanceOf(Color::class);
    }

    function it_can_be_white()
    {
        $this->beConstructedThrough('white');
        $this->shouldBeAnInstanceOf(Color::class);
    }

    function it_can_be_black()
    {
        $this->beConstructedThrough('black');
        $this->shouldBeAnInstanceOf(Color::class);
    }

    function it_cannot_have_different_color()
    {
        $this->beConstructedThrough('fromString', ['blue']);

        $this->shouldThrow(new \InvalidArgumentException('"Blue" is not a valid color in the game of chess.'))
            ->duringInstantiation();
    }

    function it_is_the_same_as_second_color()
    {
        $this->beConstructedThrough('white');
        $anotherColor = Color::white();

        $this->isSameAs($anotherColor)->shouldBe(true);
    }

    function it_is_different_when_comparing_with_another_color()
    {
        $this->beConstructedThrough('white');
        $anotherColor = Color::black();

        $this->isSameAs($anotherColor)->shouldBe(false);
    }
}
