<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Helper\TestArrangement;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Domain\PieceFactory;
use NicholasZyl\Chess\Infrastructure\Persistence\FilesystemGames;
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
     * @var FilesystemGames
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
        $this->games = new FilesystemGames(new Filesystem(new Local(__DIR__.'/../../var/games/')));
    }

    /**
     * @Given the game is set up
     * @When I setup the game
     * @throws FailureException
     * @throws Exception
     */
    public function theGameIsSetUp()
    {
        $response = $this->kernel->handle(Request::create('/', 'POST'));

        if (!$response->isSuccessful()) {
            throw new FailureException(
                sprintf("The game should be started.\nAPI response: %s", $response)
            );
        }
        $game = json_decode($response->getContent(), true);
        $this->gameId = $game['identifier'];
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
     * @When I/opponent (try to) (tries to) move(d) piece from :from to :to
     * @param string $from
     * @param string $to
     * @throws Exception
     */
    public function movePieceFromSourceToDestination(string $from, string $to)
    {
        $this->response = $this->kernel->handle(Request::create(sprintf('/%s/move', $this->gameId ?? GameId::generate()), 'POST', ['from' => $from, 'to' => $to,]));
    }

    /**
     * @When I try to find a non existing game
     */
    public function iTryToFindANonExistingGame()
    {
        $this->response = $this->kernel->handle(Request::create(sprintf('/%s', GameId::generate()), 'GET'));
    }

    /**
     * @When /I (try to )?exchange piece on (?P<position>[a-h][0-8]) for (?P<piece>[a-z]+ [a-z]+)/
     * @param string $position
     * @param string $piece
     * @throws Exception
     */
    public function exchangePieceOnPositionFor(string $position, string $piece)
    {
        $this->response = $this->kernel->handle(Request::create(sprintf('/%s/exchange', $this->gameId), 'POST', ['on' => $position, 'for' => $piece,]));
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should (?P<wasMoved>not )?be moved (to|from) (?P<position>[a-h][0-8])/
     * @param string $piece
     * @param string $position
     * @param bool $wasMoved
     * @throws FailureException
     * @throws \Exception
     */
    public function pieceShouldBeMoved(string $piece, string $position, bool $wasMoved)
    {
        if ($this->response->isSuccessful() === $wasMoved) {
            throw new FailureException(
                sprintf("API call should end with %s.\nAPI response: %s", $wasMoved ? 'failure' : 'success', $this->response)
            );
        }

        $this->pieceShouldBePlacedOnPosition($piece, $position);
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) on (?P<position>[a-h][0-8]) should be exchanged for (?P<exchangedPiece>[a-z]+ [a-z]+)/
     * @param string $position
     * @param string $exchangedPiece
     * @throws FailureException
     */
    public function pieceOnPositionShouldBeExchangedFor(string $position, string $exchangedPiece)
    {
        if (!$this->response->isSuccessful()) {
            throw new FailureException(
                sprintf("API call should end with success.\nAPI response: %s", $this->response)
            );
        }

        $this->pieceShouldBePlacedOnPosition($exchangedPiece, $position);
    }

    /**
     * @Then  /(?P<piece>[a-z]+ [a-z]+) on (?P<position>[a-h][0-8]) should not be exchanged for (?P<exchangedPiece>[a-z]+ [a-z]+)/
     * @param string $position
     * @param string $piece
     * @throws FailureException
     */
    public function pieceOnPositionShouldNotBeExchangedFor(string $position, string $piece)
    {
        if ($this->response->isSuccessful()) {
            throw new FailureException(
                sprintf("API call should end with failure.\nAPI response: %s", $this->response)
            );
        }

        $this->pieceShouldBePlacedOnPosition($piece, $position);
    }


    /**
     * Check that piece is occupying given position.
     *
     * @param string $piece
     * @param string $position
     *
     * @throws FailureException
     * @return void
     */
    private function pieceShouldBePlacedOnPosition(string $piece, string $position): void
    {
        $game = $this->getGameState();
        $board = $game['board'];
        $coordinates = CoordinatePair::fromString($position);
        if ($board[$coordinates->file()][$coordinates->rank()] !== $piece) {
            throw new FailureException(
                sprintf('%s should be placed on %s but it is not.', $piece, $position)
            );
        }
    }

    /**
     * @Then the :action is illegal
     */
    public function actionIsIllegal()
    {
        expect($this->response->isSuccessful())->shouldBe(false);
        $response = json_decode($this->response->getContent(), true);
        expect($response)->shouldHaveKey('message');
    }

    /**
     * @Then :color is (in) check(mated)
     * @param string $color
     * @throws FailureException
     */
    public function playerIsInCheck(string $color)
    {
        $game = $this->getGameState();

        expect($game)->shouldHaveKeyWithValue('checked', $color);
    }

    /**
     * @Then :color won the game
     * @param string $color
     * @throws FailureException
     */
    public function playerWonTheGame(string $color)
    {
        $game = $this->getGameState();

        expect($game)->shouldHaveKeyWithValue('winner', $color);
    }

    /**
     * @Then game should be set with initial positions of the pieces on the chessboard
     * @throws FailureException
     */
    public function gameShouldBeSetWithInitialPositionsOfThePiecesOnTheChessboard()
    {
        $game = $this->getGameState();

        expect($game)->shouldHaveKeyWithValue(
            'board',
            [
                'a' => [
                    1 => 'white rook',
                    2 => 'white pawn',
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => 'black pawn',
                    8 => 'black rook',
                ],
                'b' => [
                    1 => 'white knight',
                    2 => 'white pawn',
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => 'black pawn',
                    8 => 'black knight',
                ],
                'c' => [
                    1 => 'white bishop',
                    2 => 'white pawn',
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => 'black pawn',
                    8 => 'black bishop',
                ],
                'd' => [
                    1 => 'white queen',
                    2 => 'white pawn',
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => 'black pawn',
                    8 => 'black queen',
                ],
                'e' => [
                    1 => 'white king',
                    2 => 'white pawn',
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => 'black pawn',
                    8 => 'black king',
                ],
                'f' => [
                    1 => 'white bishop',
                    2 => 'white pawn',
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => 'black pawn',
                    8 => 'black bishop',
                ],
                'g' => [
                    1 => 'white knight',
                    2 => 'white pawn',
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => 'black pawn',
                    8 => 'black knight',
                ],
                'h' => [
                    1 => 'white rook',
                    2 => 'white pawn',
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => 'black pawn',
                    8 => 'black rook',
                ],
            ]
        );
    }

    /**
     * @Then I should not find the game
     */
    public function iShouldNotFindGame()
    {
        expect($this->response->isSuccessful())->shouldBe(false);
        expect($this->response->getStatusCode())->shouldBe(404);
    }

    /**
     * Get the current state of the game.
     *
     * @throws FailureException
     * @throws Exception
     * @return array
     */
    private function getGameState(): array
    {
        $response = $this->kernel->handle(Request::create(sprintf('/%s', $this->gameId), 'GET'));
        if (!$response->isSuccessful()) {
            throw new FailureException(sprintf("Couldn't get the current state of the game.\nAPI response: %s", $response));
        }

        return json_decode($response->getContent(), true);
    }
}
