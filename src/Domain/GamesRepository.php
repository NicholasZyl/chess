<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

interface GamesRepository
{
    /**
     * Add a game to the store under given identifier.
     *
     * @param GameId $identifier
     * @param Game $game
     *
     * @return void
     */
    public function add(GameId $identifier, Game $game): void;

    /**
     * Find the game stored with given identifier.
     *
     * @param GameId $identifier
     *
     * @return Game
     */
    public function find(GameId $identifier): Game;
}
