<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Application;

use NicholasZyl\Chess\Application\GameDto;
use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GameServiceSpec extends ObjectBehavior
{
    function let(GamesRepository $gamesRepository)
    {
        $this->beConstructedWith($gamesRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GameService::class);
    }

    function it_allows_setting_up_a_new_game(GamesRepository $gamesRepository)
    {
        $gameId = GameId::generate();
        $gamesRepository->add($gameId, Argument::type(Game::class))->shouldBeCalled();

        $this->setupGame($gameId);
    }

    function it_finds_a_game_with_given_identifier_and_returns_its_current_representation(GamesRepository $gamesRepository, Game $game)
    {
        $gameId = GameId::generate();
        $gamesRepository->find($gameId)->shouldBeCalled()->willReturn($game);
        $grid = ['a' => [1 => null,],];
        $game->board()->willReturn($grid);
        $dto = new GameDto($grid);

        $this->find($gameId)->shouldBeLike($dto);
    }

    function it_allows_moving_piece_in_a_game(GamesRepository $gamesRepository, Game $game)
    {
        $gameId = GameId::generate();
        $gamesRepository->find($gameId)->shouldBeCalled()->willReturn($game);
        $game->playMove(
            CoordinatePair::fromString('c2'),
            CoordinatePair::fromString('c3')
        )->shouldBeCalled();
        $gamesRepository->add($gameId, $game)->shouldBeCalled();

        $this->movePieceInGame($gameId, 'c2', 'c3');
    }
}
