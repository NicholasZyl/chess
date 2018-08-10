<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Infrastructure\Persistence;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Exception\GameNotFound;
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
        $filesystem->put('identifier', serialize($game))->shouldBeCalled();

        $this->add($gameId, $game);
    }

    function it_finds_a_game_by_file_with_serialised_game(Filesystem $filesystem)
    {
        $gameId = new GameId('identifier');
        $game = new Game(
            new Chessboard(),
            new LawsOfChess()
        );
        $serialisedGame = serialize($game);
        $filesystem->read('identifier')->shouldBeCalled()->willReturn($serialisedGame);

        $returnedGame = $this->find($gameId);
        $returnedGame->shouldBeAnInstanceOf(Game::class);
        $returnedGame->board()->shouldBeLike($game->board());
    }

    function it_fails_if_file_for_given_identifier_does_not_exist(Filesystem $filesystem)
    {
        $gameId = new GameId('identifier');
        $filesystem->read('identifier')->shouldBeCalled()->willThrow(FileNotFoundException::class);

        $this->shouldThrow(GameNotFound::class)->during('find', [$gameId,]);
    }
}
