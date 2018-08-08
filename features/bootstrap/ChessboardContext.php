<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Gherkin\Node\TableNode;
use Helper\PieceFactory;
use Helper\TestArrangement;
use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Domain\Piece;

/**
 * Defines application features from the specific context.
 */
class ChessboardContext implements Context, \PhpSpec\Matcher\MatchersProvider
{
    /**
     * @var PieceFactory
     */
    private $pieceFactory;

    /**
     * @var Game
     */
    private $game;

    /**
     * @var \RuntimeException
     */
    private $caughtException;

    /**
     * @var Event[]
     */
    private $occurredEvents = [];

    /**
     * @var TestArrangement
     */
    private $testArrangement;

    /**
     * ChessboardContext constructor.
     * @param PieceFactory $pieceFactory
     */
    public function __construct(PieceFactory $pieceFactory)
    {
        $this->pieceFactory = $pieceFactory;
        $this->testArrangement = new TestArrangement(new LawsOfChess());
    }

    /**
     * @Given there is a chessboard with placed pieces
     * @param TableNode $table
     */
    public function thereIsAChessboardWithPieces(TableNode $table)
    {
        foreach ($table->getHash() as $pieceAtLocation) {
            $this->testArrangement->placePieceAt(
                $this->castToPiece($pieceAtLocation['piece']),
                $this->castToCoordinates($pieceAtLocation['location'])
            );
        }
    }

    /**
     * @Given /there is a chessboard with (?P<piece>[a-z]+ [a-z]+) placed on (?P<coordinates>[a-h][0-8])/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     */
    public function thereIsAChessboardWithPiecePlacedOnSquare(Piece $piece, CoordinatePair $coordinates)
    {
        $this->testArrangement->placePieceAt($piece, $coordinates);
    }

    /**
     * @Given the game is set up
     */
    public function theGameIsSetUp()
    {
        $this->game = new Game(new Chessboard(), $this->testArrangement);
    }

    /**
     * @Given it is :color turn
     *
     * @param Color $color
     */
    public function itIsColorsTurn(Color $color): void
    {
        $this->testArrangement->setTurn($color);
    }

    /**
     * @When I/opponent (tried to) (try to) (tries to) move(d) piece from :source to :destination
     *
     * @param CoordinatePair $source
     * @param CoordinatePair $destination
     */
    public function iMovePieceFromSourceToDestination(CoordinatePair $source, CoordinatePair $destination)
    {
        try {
            $this->occurredEvents = $this->getCurrentGame()->playMove($source, $destination);
            $this->caughtException = null;
        } catch (\RuntimeException $exception) {
            $this->caughtException = $exception;
            $this->occurredEvents = [];
        }
    }

    /**
     * @When /I (try to )?exchange piece on (?P<coordinates>[a-h][0-8]) to (?P<piece>[a-z]+ [a-z]+)/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     */
    public function iExchangePieceOnPositionTo(Piece $piece, CoordinatePair $coordinates)
    {
        try {
            $this->occurredEvents = $this->getCurrentGame()->exchangePieceOnBoardTo($coordinates, $piece);
            $this->caughtException = null;
        } catch (\RuntimeException $exception) {
            $this->caughtException = $exception;
            $this->occurredEvents = [];
        }
    }

    /**
     * Get the currently tested game.
     *
     * @return Game
     */
    private function getCurrentGame(): Game
    {
        if (!$this->game) {
            $this->theGameIsSetUp();
        }

        return $this->game;
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should (?P<not>not )?be moved (?P<direction>from|to) (?P<coordinates>[a-h][0-8])/
     *
     * @param Piece $piece
     * @param bool $not
     * @param string $direction
     * @param CoordinatePair $coordinates
     */
    public function pieceShouldBeMovedTo(Piece $piece, bool $not, string $direction, CoordinatePair $coordinates)
    {
        if ($not) {
            expect($this->occurredEvents)->toNotContainEventThatPieceMoved($piece, $direction, $coordinates);
        } else {
            expect($this->occurredEvents)->toContainEventThatPieceMoved($piece, $direction, $coordinates);
        }
    }

    /**
     * @Then the :action is/was illegal
     */
    public function actionIsIllegal()
    {
        expect($this->caughtException)->shouldBeAnInstanceOf(IllegalAction::class);
        $this->caughtException = null;
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) on (?P<coordinates>[a-h][0-8]) should (?P<not>not )?be captured/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     * @param bool $not
     */
    public function pieceOnSquareShouldBeCaptured(Piece $piece, CoordinatePair $coordinates, bool $not = false)
    {
        $pieceWasCaptured = new Event\PieceWasCaptured($piece, $coordinates);
        if ($not) {
            expect($this->occurredEvents)->toNotContainEvent($pieceWasCaptured);
        } else {
            expect($this->occurredEvents)->toContainEvent($pieceWasCaptured);
        }
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) on (?P<coordinates>[a-h][0-8]) should (?P<not>not )?be exchanged with (?P<exchangedWithPiece>[a-z]+ [a-z]+)/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     * @param bool $not
     * @param Piece $exchangedWithPiece
     */
    public function pieceShouldBeExchangedWith(Piece $piece, CoordinatePair $coordinates, bool $not, Piece $exchangedWithPiece)
    {
        $pieceWasExchanged = new Event\PieceWasExchanged($piece, $exchangedWithPiece, $coordinates);
        if ($not) {
            expect($this->occurredEvents)->toNotContainEvent($pieceWasExchanged);
        } else {
            expect($this->occurredEvents)->toContainEvent($pieceWasExchanged);
        }
    }

    /**
     * @Then :color is in check
     *
     * @param Color $color
     */
    public function playerIsInCheck(Color $color)
    {
        expect($this->occurredEvents)->toContainEvent(new Event\InCheck($color));
    }

    /**
     * @Then :color is checkmated
     *
     * @param Color $color
     */
    public function playerIsCheckmated(Color $color)
    {
        expect($this->occurredEvents)->toContainEvent(new Event\Checkmated($color));
    }

    /**
     * @Then it is stalemate
     */
    public function itIsStalemate()
    {
        expect($this->occurredEvents)->toContainEvent(new Event\Stalemate());
    }

    /**
     * @Then :color won the game
     * @param Color $color
     */
    public function playerWonTheGame(Color $color)
    {
        expect($this->occurredEvents)->toContainEvent(new Event\GameEnded($color));
    }

    /**
     * @Then the game ends with drawn
     */
    public function theGameEndsWithDrawn()
    {
        expect($this->occurredEvents)->toContainEvent(new Event\GameEnded());
    }

    /**
     * @Transform :piece
     * @Transform :exchangedWithPiece
     *
     * @param string $pieceDescription
     * @return Piece
     */
    public function castToPiece(string $pieceDescription): Piece
    {
        $pieceDescription = explode(' ', $pieceDescription);
        if (count($pieceDescription) !== 2) {
            throw new \InvalidArgumentException(sprintf('Piece description "%s" is missing either rank or color'));
        }

        return $this->pieceFactory->createPieceNamedForColor($pieceDescription[1], Color::fromString($pieceDescription[0]));
    }

    /**
     * @Transform :coordinates
     * @Transform :source
     * @Transform :destination
     *
     * @param string $coordinates
     * @return CoordinatePair
     */
    public function castToCoordinates(string $coordinates): CoordinatePair
    {
        return CoordinatePair::fromString($coordinates);
    }

    /**
     * @Transform :color
     *
     * @param string $color
     *
     * @return Color
     */
    public function castToColor(string $color): Color
    {
        return Color::fromString($color);
    }

    /**
     * Check if any unexpected exception occurred.
     *
     * @BeforeStep
     * @param BeforeStepScope $scope
     */
    public function checkForExceptionBetweenSteps(BeforeStepScope $scope): void
    {
        if ($this->caughtException && strpos($scope->getStep()->getText(), 'illegal') === false) {
            throw $this->caughtException;
        }
    }

    /**
     * Get helper matcher functions for `expect` function.
     *
     * @return array
     */
    public function getMatchers(): array
    {
        return [
            'containEvent' => function (array $occurredEvents, Event $event) {
                /** @var Event $occurredEvent */
                foreach ($occurredEvents as $occurredEvent) {
                    if ($occurredEvent->equals($event)) {
                        return true;
                    }
                }

                return false;
            },
            'containEventThatPieceMoved' => function (array $occurredEvents, Piece $piece, string $direction, Coordinates $coordinates) {
                foreach ($occurredEvents as $occurredEvent) {
                    if ($occurredEvent instanceof Event\PieceWasMoved && $occurredEvent->piece()->isSameAs($piece)) {
                        $expectedCoordinates = $direction === 'from' ? $occurredEvent->source() : $occurredEvent->destination();

                        if ($expectedCoordinates->equals($coordinates)) {
                            return true;
                        }
                    }
                }

                return false;
            },
        ];
    }
}
