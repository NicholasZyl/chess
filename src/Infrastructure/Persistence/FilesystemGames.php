<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Infrastructure\Persistence;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use NicholasZyl\Chess\Domain\Exception\GameNotFound;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;

final class FilesystemGames implements GamesRepository
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Create games repository persisted a files in the file system.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function add(GameId $identifier, Game $game): void
    {
        $this->filesystem->put($identifier->id(), serialize($game));
    }

    /**
     * {@inheritdoc}
     */
    public function find(GameId $identifier): Game
    {
        try {
            $storedGame = $this->filesystem->read($identifier->id());

            return unserialize($storedGame);
        } catch (FileNotFoundException $fileNotFoundException) {
            throw new GameNotFound($identifier);
        }
    }
}
