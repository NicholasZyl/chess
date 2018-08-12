<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Helper\TestArrangement;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Domain\PieceFactory;
use NicholasZyl\Chess\Infrastructure\Persistence\FilesystemGames;
use NicholasZyl\Chess\UI\Console\Application;
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
    A B C D E F G H
CHESS_END
        );
    }
}