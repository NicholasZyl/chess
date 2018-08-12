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
use NicholasZyl\Chess\UI\Console\Application;
use NicholasZyl\Chess\UI\Console\AsciiTerminalDisplay;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\HttpKernel\KernelInterface;

class ConsoleContext implements Context
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
     * @var ApplicationTester
     */
    private $tester;

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

        $application = new Application($kernel);
        $application->setAutoExit(false);
        $this->tester = new ApplicationTester($application);
    }

    /**
     * @Given the game is set up
     * @When I setup the game
     */
    public function iSetupTheGame()
    {
        $this->tester->run(['command' => 'start',]);
        $output = $this->tester->getDisplay();
        [$identifier] = sscanf($output, 'Game was setup with id %s');
        $this->gameId = new GameId($identifier);
    }

    /**
     * @Given /there is a chessboard with (?P<piece>[a-zA-Z]+ [a-z]+) placed on (?P<position>[a-h][0-8])/
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
     * @When I try to find a non existing game
     */
    public function iTryToFindANonExistingGame()
    {
        $this->tester->run(['command' => 'display', '--id' => GameId::generate()->id(),]);
    }

    /**
     * @When I/opponent (try to) (tries to) (tried to) move(d) piece from :from to :to
     * @param string $from
     * @param string $to
     */
    public function movePieceFromSourceToDestination(string $from, string $to)
    {
        $this->tester->run(['command' => 'move', 'from' => $from, 'to' => $to, '--id' => ($this->gameId ?? GameId::generate())->id(),]);
    }

    /**
     * @When /I (try to )?exchange piece on (?P<position>[a-h][0-8]) for (?P<piece>[a-zA-Z]+ [a-z]+)/
     * @param string $position
     * @param string $piece
     */
    public function exchangePieceOnPositionFor(string $position, string $piece)
    {
        $this->tester->run(['command' => 'exchange', 'on' => $position, 'for' => $piece, '--id' => ($this->gameId ?? GameId::generate())->id(),]);
    }

    /**
     * @Then game should be set with initial positions of the pieces on the chessboard
     */
    public function gameShouldBeSetWithInitialPositionsOfThePiecesOnTheChessboard()
    {
        $this->tester->run(['command' => 'display', '--id' => $this->gameId->id(),]);
        $output = $this->tester->getDisplay();

        expect($output)->shouldContain(
<<<CHESS_END
  -------------------
8 | r n b q k b n r |
7 | p p p p p p p p |
6 |                 |
5 |                 |
4 |                 |
3 |                 |
2 | P P P P P P P P |
1 | R N B Q K B N R |
  -------------------
    a b c d e f g h
CHESS_END
        );
    }

    /**
     * @Then I should not find the game
     */
    public function iShouldNotFindTheGame()
    {
        expect($this->tester->getDisplay())->shouldContain('Game was not found');
    }

    /**
     * @Then the :action is/was illegal
     * @param string $action
     */
    public function theActionIsIllegal(string $action)
    {
        expect($this->tester->getDisplay())->shouldContain(sprintf('%s was not possible', ucfirst($action)));
    }

    /**
     * @Then /(?P<piece>[a-zA-Z]+ [a-z]+) should (not )?be moved (to|from) (?P<position>[a-h][0-8])/
     * @Then /(?P<piece>[a-zA-Z]+ [a-z]+) on (?P<position>[a-h][0-8]) should not be exchanged for (?P<exchangedPiece>[a-zA-Z]+ [a-z]+)/
     * @param string $piece
     * @param string $position
     */
    public function pieceShouldBePlacedOn(string $piece, string $position)
    {
        $this->tester->run(['command' => 'display', '--id' => $this->gameId->id(),]);
        $output = $this->tester->getDisplay();

        $board = explode(PHP_EOL, $output);
        $rank = 8 - intval($position[1]) + 1;
        $file = 4 + (ord($position[0]) - ord('a')) * 2;

        $actualPiece = $board[$rank][$file];
        $pieceDescription = explode(' ', $piece);
        $expectedPiece = (new AsciiTerminalDisplay())->visualisePiece($pieceDescription[0], $pieceDescription[1]);

        expect($actualPiece)->shouldBe($expectedPiece);
    }

    /**
     * @Then /(?P<piece>[a-zA-Z]+ [a-z]+) on (?P<position>[a-h][0-8]) should be exchanged for (?P<exchangedPiece>[a-zA-Z]+ [a-z]+)/
     * @param string $position
     * @param string $exchangedPiece
     */
    public function pieceOnPositionShouldBeExchangedFor(string $position, string $exchangedPiece)
    {
        $this->pieceShouldBePlacedOn($exchangedPiece, $position);
    }
}