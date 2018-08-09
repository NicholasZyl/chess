<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Helper\InMemoryGames;
use Helper\TestArrangement;
use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Domain\PieceFactory;
use PhpSpec\Exception\Example\FailureException;

class AppContext implements Context
{
    /**
     * @var PieceFactory
     */
    private $pieceFactory;

    /**
     * @var TestArrangement
     */
    private $testArrangement;

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
        $this->pieceFactory = new PieceFactory();
        $this->testArrangement = new TestArrangement(new LawsOfChess());
        $this->games = new InMemoryGames();
        $this->gameService = new GameService(
            $this->games,
            $this->pieceFactory
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
     * @Given /there is a chessboard with (?P<piece>[a-z]+ [a-z]+) placed on (?P<position>[a-h][0-8])/
     * @param string $piece
     * @param string $position
     */
    public function thereIsAChessboardWithPiecePlacedOnPosition(string $piece, string $position)
    {
        $piece = $this->pieceFactory->createPieceFromDescription($piece);
        $this->testArrangement->placePieceAt($piece, CoordinatePair::fromString($position));

        $this->gameId = GameId::generate();
        $this->games->add($this->gameId, new Game(new Chessboard(), $this->testArrangement));
    }

    /**
     * @Given there is a chessboard with placed pieces
     * @param TableNode $table
     */
    public function thereIsAChessboardWithPlacedPieces(TableNode $table)
    {
        foreach ($table->getHash() as $pieceAtLocation) {
            $this->testArrangement->placePieceAt(
                $this->pieceFactory->createPieceFromDescription($pieceAtLocation['piece']),
                CoordinatePair::fromString($pieceAtLocation['location'])
            );
        }

        $this->gameId = GameId::generate();
        $this->games->add($this->gameId, new Game(new Chessboard(), $this->testArrangement));
    }


    /**
     * @When I/opponent (tries to) move(d) piece from :source to :destination
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
     * @When /I exchange piece on (?P<position>[a-h][0-8]) to (?P<piece>[a-z]+ [a-z]+)/
     * @param string $position
     * @param string $piece
     */
    public function iExchangePieceOnPositionTo(string $position, string $piece)
    {
        try {
            $this->gameService->exchangePieceInGame($this->gameId, $position, $piece);
        } catch (\Exception $e) {
            $this->caughtException = $e;
        }
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should be moved to (?P<position>[a-h][0-8])/
     * @param string $piece
     * @param string $position
     * @throws FailureException
     */
    public function pieceShouldBeMovedToPosition(string $piece, string $position)
    {
        $this->pieceShouldBePlacedOnPosition($piece, $position);
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should not be moved from (?P<position>[a-h][0-8])/
     * @param string $piece
     * @param string $position
     * @throws FailureException
     */
    public function pieceShouldNotBeMovedFromPosition(string $piece, string $position)
    {
        $this->pieceShouldBePlacedOnPosition($piece, $position);
    }

    /**
     * @Then the move is illegal
     * @throws FailureException
     */
    public function theMoveIsIllegal()
    {
        if (!$this->caughtException instanceof IllegalAction) {
            throw new FailureException(
                sprintf('Expected move to be illegal but got "%s"', $this->caughtException ? $this->caughtException->getMessage() : 'none')
            );
        }
        $this->caughtException = null;
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) on (?P<position>[a-h][0-8]) should be exchanged with (?P<exchangedPiece>[a-z]+ [a-z]+)/
     * @param string $position
     * @param string $exchangedPiece
     * @throws FailureException
     */
    public function pieceOnPositionShouldBeExchangedWith(string $position, string $exchangedPiece)
    {
        $this->pieceShouldBePlacedOnPosition($exchangedPiece, $position);
    }

    /**
     * @param string $piece
     * @param string $position
     *
     * @throws FailureException
     * @return void
     */
    private function pieceShouldBePlacedOnPosition(string $piece, string $position): void
    {
        $board = $this->gameService->find($this->gameId)->board();
        $actualPiece = $board->position($position[0], (int)$position[1]);

        if ($piece !== $actualPiece) {
            throw new FailureException(
                sprintf('%s should be placed on %s but it is not.', $piece, $position)
            );
        }
    }

    /**
     * @Then :color is checkmated
     * @param string $color
     * @throws FailureException
     */
    public function playerIsCheckmated(string $color)
    {
        $game = $this->gameService->find($this->gameId);

        if ($game->checked() !== ucfirst($color)) {
            throw new FailureException(sprintf('%s should be checkmated.', $color));
        }
    }

    /**
     * @Then :color won the game
     * @param string $color
     * @throws FailureException
     */
    public function playerWonTheGame(string $color)
    {
        $game = $this->gameService->find($this->gameId);

        if (!$game->isEnded()) {
            throw new FailureException('The game should be ended.');
        }

        if ($game->winner() !== ucfirst($color)) {
            throw new FailureException(sprintf('%s should be the winner.', $color));
        }
    }

}