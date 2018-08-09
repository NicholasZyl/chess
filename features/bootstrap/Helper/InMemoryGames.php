<?php
declare(strict_types=1);

namespace Helper;

use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;

class InMemoryGames implements GamesRepository
{
    private $games = [];

    public function add(GameId $identifier, Game $game): void
    {
        $this->games[$identifier->id()] = $game;
    }

    public function find(GameId $identifier): Game
    {
        return $this->games[$identifier->id()];
    }
}