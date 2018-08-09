<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\GameId;
use PhpSpec\ObjectBehavior;

class GameIdSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('rand');

        $this->shouldHaveType(GameId::class);
    }

    function it_is_identifier_for_a_game()
    {
        $this->beConstructedWith('rand');
        $this->id()->shouldBe('rand');
    }

    function it_generates_string_as_id()
    {
        $this->beConstructedThrough('generate');

        $this->id()->shouldBeString();
    }
}
