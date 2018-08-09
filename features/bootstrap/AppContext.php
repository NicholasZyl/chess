<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Helper\InMemoryGames;
use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\GameId;

class AppContext implements Context
{
    /**
     * @var InMemoryGames
     */
    private $games;

    /**
     * @var GameService
     */
    private $gameService;

    /**
     * @var GameId
     */
    private $gameId;

    /**
     * @var Exception
     */
    private $caughtException;

    /**
     * AppContext constructor.
     */
    public function __construct()
    {
        $this->games = new InMemoryGames();
        $this->gameService = new GameService(
            $this->games
        );
    }

    /**
     * @Given the game is set up
     */
    public function theGameIsSetUp()
    {
        $this->gameId = GameId::generate();
        $this->gameService->setupGame($this->gameId);
    }

    /**
     * @When I/opponent (tries to) move piece from :source to :destination
     * @param string $source
     * @param string $destination
     */
    public function iMovePieceFromSourceToDestination(string $source, string $destination)
    {
        try {
            $this->gameService->movePieceInGame($this->gameId, $source, $destination);
        } catch (\Exception $e) {
            $this->caughtException = $e;
        }
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should be moved to (?P<position>[a-h][0-8])/
     * @param string $piece
     * @param string $position
     * @throws \PhpSpec\Exception\Example\FailureException
     */
    public function pieceShouldBeMovedToPosition(string $piece, string $position)
    {
        $board = $this->gameService->find($this->gameId);
        $actualPiece = $board->position($position[0], (int)$position[1]);

        if ($piece !== $actualPiece) {
            throw new \PhpSpec\Exception\Example\FailureException(
                sprintf('%s should be moved to %s but it was not.', $piece, $position)
            );
        }
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should not be moved from (?P<position>[a-h][0-8])/
     * @param string $piece
     * @param string $position
     * @throws \PhpSpec\Exception\Example\FailureException
     */
    public function pieceShouldNotBeMovedFromPosition(string $piece, string $position)
    {
        $board = $this->gameService->find($this->gameId);
        $actualPiece = $board->position($position[0], (int)$position[1]);

        if ($piece !== $actualPiece) {
            throw new \PhpSpec\Exception\Example\FailureException(
                sprintf('%s should not be moved from %s but it was.', $piece, $position)
            );
        }
    }

    /**
     * @Then the move is illegal
     * @throws \PhpSpec\Exception\Example\FailureException
     */
    public function theMoveIsIllegal()
    {
        if (!$this->caughtException instanceof  \NicholasZyl\Chess\Domain\Exception\IllegalAction) {
            throw new \PhpSpec\Exception\Example\FailureException(
                sprintf('Expected move to be illegal but got "%s"', $this->caughtException ? $this->caughtException->getMessage() : 'none')
            );
        }
        $this->caughtException = null;
    }
}