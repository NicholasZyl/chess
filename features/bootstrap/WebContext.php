<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Helper\TestArrangement;
use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Domain\PieceFactory;
use PhpSpec\Exception\Example\FailureException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class WebContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var PieceFactory
     */
    private $pieceFactory;

    /**
     * @var TestArrangement
     */
    private $testArrangement;

    /**
     * @var \NicholasZyl\Chess\Domain\GamesRepository
     */
    private $games;

    /**
     * @var GameId
     */
    private $gameId;

    /**
     * @var Response
     */
    private $response;

    /**
     * Create context for web UI.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->pieceFactory = new PieceFactory();
        $this->testArrangement = new TestArrangement(new LawsOfChess());
        $this->games = new \Helper\InMemoryGames();
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
     * @When I/opponent (try to) (tries to) move(d) piece from :from to :to
     * @param string $from
     * @param string $to
     * @throws Exception
     */
    public function movePieceFromSourceToDestination(string $from, string $to)
    {
        $this->response = $this->kernel->handle(Request::create(sprintf('/%s/move', $this->gameId), 'POST', ['from' => $from, 'to' => $to,]));
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should (not )?be moved (to|from) (?P<position>[a-h][0-8])/
     * @param string $piece
     * @param string $position
     * @throws FailureException
     * @throws Exception
     */
    public function pieceShouldBeMoved(string $piece, string $position)
    {
        if (!$this->response->isSuccessful()) {
            throw new FailureException(
                sprintf("Couldn't move the piece.\nAPI response: %s", $this->response)
            );
        }

        $response = $this->kernel->handle(Request::create(sprintf('/%s', $this->gameId), 'GET'));

        if (!$response->isSuccessful()) {
            throw new FailureException("Couldn't get the current state of the game.");
        }
        $game = json_decode($response->getContent());
        $board = $game['board'];
        $coordinates = CoordinatePair::fromString($position);
        if ($board[$coordinates->file()][$coordinates->rank()] !== $piece) {
            throw new FailureException(
                sprintf('%s should be placed on %s but it is not.', $piece, $position)
            );
        }
    }

    /**
     * @Then the move is illegal
     * @throws FailureException
     */
    public function theMoveIsIllegal()
    {
        if ($this->response->isSuccessful()) {
            throw new FailureException("Move shouldn't be possible but it was.\nAPI response: %s", $this->response);
        }
        $response = json_decode($this->response->getContent());
        if (!array_key_exists('message', $response)) {
            throw new FailureException("API response is missing error message.\nAPI response: %s", $this->response);
        }
    }
}
