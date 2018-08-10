<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Infrastructure\Persistence;

use League\Flysystem\Filesystem;
use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Infrastructure\Persistence\FilesystemGames;
use PhpSpec\ObjectBehavior;

class FilesystemGamesSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem)
    {
        $this->beConstructedWith($filesystem, '/var/games/');
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType(FilesystemGames::class);
    }

    function it_is_games_repository_implementation()
    {
        $this->shouldBeAnInstanceOf(GamesRepository::class);
    }

    function it_stores_a_serialised_game_in_a_file(Filesystem $filesystem)
    {
        $gameId = new GameId('identifier');
        $game = new Game(
            new Chessboard(),
            new LawsOfChess()
        );
        $filesystem->put('/var/games/identifier', serialize($game))->shouldBeCalled();

        $this->add($gameId, $game);
    }

    function it_finds_a_game_with_by_file_with_serialised_game(Filesystem $filesystem)
    {
        $gameId = new GameId('identifier');
        $game = new Game(
            new Chessboard(),
            new LawsOfChess()
        );
        $serialisedGame = serialize($game);
        $filesystem->read('/var/games/identifier')->shouldBeCalled()->willReturn($serialisedGame);

        $this->find($gameId)->shouldBeLike($game);
    }
}
