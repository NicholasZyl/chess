<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Application;

use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;
use NicholasZyl\Chess\Domain\LawsOfChess;

final class GameService
{
    /**
     * @var GamesRepository
     */
    private $games;

    /**
     * Create Game Service to interact with the application.
     *
     * @param GamesRepository $games
     */
    public function __construct(GamesRepository $games)
    {
        $this->games = $games;
    }

    /**
     * Setup a new game with given identifier.
     *
     * @param GameId $identifier
     *
     * @return void
     */
    public function setupGame(GameId $identifier): void
    {
        $game = new Game(
            new Chessboard(),
            new LawsOfChess()
        );

        $this->games->add($identifier, $game);
    }

    /**
     * Find a game with given identifier.
     *
     * @param GameId $identifier
     *
     * @return GameDto
     */
    public function find(GameId $identifier): GameDto
    {
        $game = $this->games->find($identifier);

        return new GameDto($game->board());
    }

    /**
     * Move a piece between two squares in a game with given identifier.
     *
     * @param GameId $identifier
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    public function movePieceInGame(GameId $identifier, string $from, string $to): void
    {
        $game = $this->games->find($identifier);
        $game->playMove(
            CoordinatePair::fromString($from),
            CoordinatePair::fromString($to)
        );
        $this->games->add($identifier, $game);
    }
}
