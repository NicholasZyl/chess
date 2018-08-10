<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Exception\GameNotFound;
use NicholasZyl\Chess\Domain\GameId;
use PhpSpec\ObjectBehavior;

class GameNotFoundSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new GameId('id'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GameNotFound::class);
    }

    function it_is_runtime_exception()
    {
        $this->shouldBeAnInstanceOf(\RuntimeException::class);
    }

    function it_fails_for_provided_identifier()
    {
        $this->id()->shouldBeLike(new GameId('id'));
    }
}
