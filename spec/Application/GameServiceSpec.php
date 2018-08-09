<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Application;

use NicholasZyl\Chess\Application\GameDto;
use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;
use NicholasZyl\Chess\Domain\Piece\Queen;
use NicholasZyl\Chess\Domain\PieceFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GameServiceSpec extends ObjectBehavior
{
    function let(GamesRepository $gamesRepository, PieceFactory $pieceFactory)
    {
        $this->beConstructedWith($gamesRepository, $pieceFactory);
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

    function it_allows_exchanging_piece_in_a_game(GamesRepository $gamesRepository, PieceFactory $pieceFactory, Game $game)
    {
        $gameId = GameId::generate();
        $gamesRepository->find($gameId)->shouldBeCalled()->willReturn($game);
        $queen = Queen::forColor(Color::white());
        $pieceFactory->createPieceFromDescription('white queen')->shouldBeCalled()->willReturn($queen);
        $game->exchangePieceOnBoardTo(
            CoordinatePair::fromString('d8'),
            $queen
        )->shouldBeCalled();
        $gamesRepository->add($gameId, $game)->shouldBeCalled();

        $this->exchangePieceInGame($gameId, 'd8', 'white queen');
    }
}
