<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Application;

use NicholasZyl\Chess\Application\Dto\BoardDto;
use NicholasZyl\Chess\Application\Dto\GameDto;
use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Exception\BoardException;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\GamesRepository;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Domain\PieceFactory;

final class GameService
{
    /**
     * @var GamesRepository
     */
    private $games;

    /**
     * @var PieceFactory
     */
    private $pieceFactory;

    /**
     * Create Game Service to interact with the application.
     *
     * @param GamesRepository $games
     * @param PieceFactory $pieceFactory
     */
    public function __construct(GamesRepository $games, PieceFactory $pieceFactory)
    {
        $this->games = $games;
        $this->pieceFactory = $pieceFactory;
    }

    /**
     * Setup a new game with generated identifier.
     *
     * @return GameId
     */
    public function setupGame(): GameId
    {
        $identifier = GameId::generate();
        $game = new Game(
            new Chessboard(),
            new LawsOfChess()
        );

        $this->games->add($identifier, $game);

        return $identifier;
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

        return new GameDto(
            new BoardDto($game->board()),
            (string)$game->checked(),
            $game->hasEnded(),
            (string)$game->winner()
        );
    }

    /**
     * Move a piece between two squares in a game with given identifier.
     *
     * @param GameId $identifier
     * @param string $from
     * @param string $to
     *
     * @throws BoardException
     * @throws IllegalAction
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

    /**
     * Exchange piece in a game with given identifier.
     *
     * @param GameId $identifier
     * @param string $position
     * @param string $pieceDescription
     *
     * @throws BoardException
     * @throws IllegalAction
     *
     * @return void
     */
    public function exchangePieceInGame(GameId $identifier, string $position, string $pieceDescription)
    {
        $game = $this->games->find($identifier);
        $game->exchangePieceOnBoardTo(
            CoordinatePair::fromString($position),
            $this->pieceFactory->createPieceFromDescription($pieceDescription)
        );
        $this->games->add($identifier, $game);
    }
}
