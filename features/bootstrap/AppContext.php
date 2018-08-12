<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Helper\InMemoryGames;
use Helper\TestArrangement;
use NicholasZyl\Chess\Application\Dto\PieceDto;
use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Exception\GameNotFound;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Domain\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece\Rook;
use NicholasZyl\Chess\Domain\PieceFactory;

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
     * @When I setup the game
     */
    public function theGameIsSetUp()
    {
        $this->gameId = $this->gameService->setupGame();
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
     * @When I/opponent (try to) (tries to) move(d) piece from :from to :to
     * @param string $from
     * @param string $to
     */
    public function movePieceFromSourceToDestination(string $from, string $to)
    {
        try {
            $this->gameService->movePieceInGame($this->gameId ?? GameId::generate(), $from, $to);
        } catch (\Exception $e) {
            $this->caughtException = $e;
        }
    }

    /**
     * @When /I exchange piece on (?P<position>[a-h][0-8]) for (?P<piece>[a-zA-Z]+ [a-z]+)/
     * @param string $position
     * @param string $piece
     */
    public function exchangePieceOnPositionFor(string $position, string $piece)
    {
        try {
            $this->gameService->exchangePieceInGame($this->gameId ?? GameId::generate(), $position, $piece);
        } catch (\Exception $e) {
            $this->caughtException = $e;
        }
    }

    /**
     * @When I try to find a non existing game
     */
    public function iTryToFindANonExistingGame()
    {
        try {
            $this->gameService->find(new GameId('not-existing'));
        } catch (\Exception $e) {
            $this->caughtException = $e;
        }
    }

    /**
     * @Then /(?P<piece>[a-zA-Z]+ [a-z]+) should be moved to (?P<position>[a-h][0-8])/
     * @param string $piece
     * @param string $position
     */
    public function pieceShouldBeMovedToPosition(string $piece, string $position)
    {
        $this->pieceShouldBePlacedOnPosition($piece, $position);
    }

    /**
     * @Then /(?P<piece>[a-zA-Z]+ [a-z]+) should not be moved from (?P<position>[a-h][0-8])/
     * @param string $piece
     * @param string $position
     */
    public function pieceShouldNotBeMovedFromPosition(string $piece, string $position)
    {
        $this->pieceShouldBePlacedOnPosition($piece, $position);
    }

    /**
     * @Then the move is illegal
     */
    public function theMoveIsIllegal()
    {
        expect($this->caughtException)->shouldBeAnInstanceOf(IllegalAction::class);
        $this->caughtException = null;
    }

    /**
     * @Then /(?P<piece>[a-zA-Z]+ [a-z]+) on (?P<position>[a-h][0-8]) should be exchanged for (?P<exchangedPiece>[a-zA-Z]+ [a-z]+)/
     * @param string $position
     * @param string $exchangedPiece
     */
    public function pieceOnPositionShouldBeExchangedFor(string $position, string $exchangedPiece)
    {
        $this->pieceShouldBePlacedOnPosition($exchangedPiece, $position);
    }

    /**
     * @param string $piece
     * @param string $position
     *
     * @return void
     */
    private function pieceShouldBePlacedOnPosition(string $piece, string $position): void
    {
        $board = $this->gameService->find($this->gameId)->board();
        $actualPiece = $board->position($position[0], (int)$position[1]);

        $pieceDescription = explode(' ', $piece);
        expect($actualPiece)->shouldBeLike(new PieceDto($pieceDescription[0], $pieceDescription[1]));
    }

    /**
     * @Then :color is (in) check(mated)
     * @param string $color
     */
    public function playerIsChecked(string $color)
    {
        $game = $this->gameService->find($this->gameId);

        expect($game->checked())->shouldBe($color);
    }

    /**
     * @Then :color won the game
     * @param string $color
     */
    public function playerWonTheGame(string $color)
    {
        $game = $this->gameService->find($this->gameId);

        expect($game->isEnded())->shouldBe(true);

        expect($game->winner())->shouldBe($color);
    }


    /**
     * @Then game should be set with initial positions of the pieces on the chessboard
     */
    public function gameShouldBeSetWithInitialPositionsOfThePiecesOnTheChessboard()
    {
        $game = $this->gameService->find($this->gameId);

        expect($game->board())->shouldBeLike(
            new \NicholasZyl\Chess\Application\Dto\BoardDto(
            [
                'a' => [
                    1 => Rook::forColor(Color::white()),
                    2 => Pawn::forColor(Color::white()),
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => Pawn::forColor(Color::black()),
                    8 => Rook::forColor(Color::black()),
                ],
                'b' => [
                    1 => Knight::forColor(Color::white()),
                    2 => Pawn::forColor(Color::white()),
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => Pawn::forColor(Color::black()),
                    8 => Knight::forColor(Color::black()),
                ],
                'c' => [
                    1 => Bishop::forColor(Color::white()),
                    2 => Pawn::forColor(Color::white()),
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => Pawn::forColor(Color::black()),
                    8 => Bishop::forColor(Color::black()),
                ],
                'd' => [
                    1 => Queen::forColor(Color::white()),
                    2 => Pawn::forColor(Color::white()),
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => Pawn::forColor(Color::black()),
                    8 => Queen::forColor(Color::black()),
                ],
                'e' => [
                    1 => King::forColor(Color::white()),
                    2 => Pawn::forColor(Color::white()),
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => Pawn::forColor(Color::black()),
                    8 => King::forColor(Color::black()),
                ],
                'f' => [
                    1 => Bishop::forColor(Color::white()),
                    2 => Pawn::forColor(Color::white()),
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => Pawn::forColor(Color::black()),
                    8 => Bishop::forColor(Color::black()),
                ],
                'g' => [
                    1 => Knight::forColor(Color::white()),
                    2 => Pawn::forColor(Color::white()),
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => Pawn::forColor(Color::black()),
                    8 => Knight::forColor(Color::black()),
                ],
                'h' => [
                    1 => Rook::forColor(Color::white()),
                    2 => Pawn::forColor(Color::white()),
                    3 => '',
                    4 => '',
                    5 => '',
                    6 => '',
                    7 => Pawn::forColor(Color::black()),
                    8 => Rook::forColor(Color::black()),
                ],
            ])
        );
    }

    /**
     * @Then I should not find the game
     */
    public function iShouldNotFindGame()
    {
        expect($this->caughtException)->shouldBeAnInstanceOf(GameNotFound::class);
    }
}