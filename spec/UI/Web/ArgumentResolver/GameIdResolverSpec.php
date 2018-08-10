<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\UI\Web\ArgumentResolver;

use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\UI\Web\ArgumentResolver\GameIdResolver;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class GameIdResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(GameIdResolver::class);
    }

    function it_is_argument_resolver()
    {
        $this->shouldBeAnInstanceOf(ArgumentValueResolverInterface::class);
    }

    function it_supports_arguments_with_game_id_type(Request $request)
    {
        $argument = new ArgumentMetadata('identifier', GameId::class, false, false, null);
        $this->supports($request, $argument)->shouldBe(true);

        $argument = new ArgumentMetadata('identifier', Game::class, false, false, null);
        $this->supports($request, $argument)->shouldBe(false);
    }

    function it_converts_game_id_value_from_the_request_to_object()
    {
        $request = new Request([], [], ['gameId' => 'identifier',]);
        $argument = new ArgumentMetadata('identifier', GameId::class, false, false, null);

        $this->resolve($request, $argument)->shouldYieldLike([new GameId('identifier')]);
    }
}
