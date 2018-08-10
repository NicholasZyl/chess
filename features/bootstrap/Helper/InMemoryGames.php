<?php
declare(strict_types=1);

namespace Helper;

use NicholasZyl\Chess\Domain\Exception\GameNotFound;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;

class InMemoryGames implements GamesRepository
{
    /**
     * @var Game[]
     */
    private $games = [];

    /**
     * {@inheritdoc}
     */
    public function add(GameId $identifier, Game $game): void
    {
        $this->games[$identifier->id()] = $game;
    }

    /**
     * {@inheritdoc}
     */
    public function find(GameId $identifier): Game
    {
        if (!array_key_exists($identifier->id(), $this->games)) {
            throw new GameNotFound($identifier);
        }

        return $this->games[$identifier->id()];
    }
}