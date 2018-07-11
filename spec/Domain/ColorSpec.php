<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Color;
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

        $this->is($anotherColor)->shouldBe(true);
    }

    function it_is_different_when_comparing_with_another_color()
    {
        $this->beConstructedThrough('white');
        $anotherColor = Color::black();

        $this->is($anotherColor)->shouldBe(false);
    }

    function it_has_black_as_opponent_of_white()
    {
        $this->beConstructedThrough('white');

        $this->opponent()->shouldBeLike(Color::black());
    }

    function it_has_white_as_opponent_of_black()
    {
        $this->beConstructedThrough('black');

        $this->opponent()->shouldBeLike(Color::white());
    }
}
