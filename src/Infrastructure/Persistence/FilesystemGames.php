<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Infrastructure\Persistence;

use League\Flysystem\Filesystem;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;

final class FilesystemGames implements GamesRepository
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Add a game to the store under given identifier.
     *
     * @param GameId $identifier
     * @param Game $game
     *
     * @return void
     */
    public function add(GameId $identifier, Game $game): void
    {
        $this->filesystem->put($identifier->id(), serialize($game));
    }

    /**
     * Find the game stored with given identifier.
     *
     * @param GameId $identifier
     *
     * @return Game
     */
    public function find(GameId $identifier): Game
    {
        $storedGame = $this->filesystem->read($identifier->id());

        return unserialize($storedGame);
    }
}
