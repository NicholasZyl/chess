<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Application;

use NicholasZyl\Chess\Application\Dto\BoardDto;
use NicholasZyl\Chess\Application\Dto\GameDto;
use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;
use NicholasZyl\Chess\Domain\Piece\Pawn;
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
        $gamesRepository->add(Argument::type(GameId::class), Argument::type(Game::class))->shouldBeCalled();

        $this->setupGame()->shouldBeAnInstanceOf(GameId::class);
    }

    function it_finds_a_game_with_given_identifier_and_returns_its_current_representation(GamesRepository $gamesRepository, Game $game)
    {
        $gameId = GameId::generate();
        $gamesRepository->find($gameId)->shouldBeCalled()->willReturn($game);
        $grid = ['a' => [1 => null,],];
        $game->board()->willReturn($grid);
        $game->checked()->willReturn(Color::black());
        $game->hasEnded()->willReturn(false);
        $game->winner()->willReturn(null);
        $dto = new GameDto(
            new BoardDto($grid),
            'Black'
        );

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
        $pieceFactory->createPieceNamedForColor('queen', 'White')->shouldBeCalled()->willReturn($queen);
        $game->exchangePieceOnBoardTo(
            CoordinatePair::fromString('d8'),
            $queen
        )->shouldBeCalled();
        $gamesRepository->add($gameId, $game)->shouldBeCalled();

        $this->exchangePieceInGame($gameId, 'd8', 'White queen');
    }

    function it_exchanges_piece_to_same_color_if_not_provided(GamesRepository $gamesRepository, PieceFactory $pieceFactory, Game $game)
    {
        $gameId = GameId::generate();
        $gamesRepository->find($gameId)->shouldBeCalled()->willReturn($game);
        $queen = Queen::forColor(Color::white());
        $game->board()->willReturn(['d' => [8 => Pawn::forColor(Color::white()),],]);
        $pieceFactory->createPieceNamedForColor('queen', 'White')->shouldBeCalled()->willReturn($queen);
        $game->exchangePieceOnBoardTo(
            CoordinatePair::fromString('d8'),
            $queen
        )->shouldBeCalled();
        $gamesRepository->add($gameId, $game)->shouldBeCalled();

        $this->exchangePieceInGame($gameId, 'd8', 'queen');
    }
}
