<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Helper\PieceFactory;
use Helper\PieceTestPositions;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Chessboard;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;

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
     * ChessboardContext constructor.
     * @param PieceFactory $pieceFactory
     */
    public function __construct(PieceFactory $pieceFactory)
    {
        $this->pieceFactory = $pieceFactory;
    }

    /**
     * @Given there is a chessboard with placed pieces
     * @param TableNode $table
     */
    public function thereIsAChessboardWithPieces(TableNode $table)
    {
        $initialPositions = new PieceTestPositions();

        foreach ($table->getHash() as $pieceAtLocation) {
            $initialPositions->placePieceAt(
                $this->castToPiece($pieceAtLocation['piece']),
                $this->castToCoordinates($pieceAtLocation['location'])
            );
        }

        $this->setupGame($initialPositions);
    }

    /**
     * @Given /there is a chessboard with (?P<piece>[a-z]+ [a-z]+) placed on (?P<coordinates>[a-h][0-8])/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     */
    public function thereIsAChessboardWithPiecePlacedOnSquare(Piece $piece, CoordinatePair $coordinates)
    {
        $initialPositions = new PieceTestPositions();
        $initialPositions->placePieceAt($piece, $coordinates);
        $this->setupGame($initialPositions);
    }

    /**
     * Setup board with pieces at their initial positions.
     *
     * @param Piece\InitialPositions $initialPositions
     *
     * @return void
     */
    private function setupGame(Piece\InitialPositions $initialPositions): void
    {
        $laws = new \NicholasZyl\Chess\Domain\Fide\LawsOfChess();
        $this->game = new Game(new Chessboard(), $initialPositions, $laws->rules());
    }

    /**
     * @When I/opponent (tried to) (try to) move(d) piece from :source to :destination
     *
     * @param CoordinatePair $source
     * @param CoordinatePair $destination
     */
    public function iMovePieceFromSourceToDestination(CoordinatePair $source, CoordinatePair $destination)
    {
        try {
            $this->occurredEvents = $this->game->playMove($source, $destination);
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
            $this->occurredEvents = $this->game->exchangePieceOnBoardTo($coordinates, $piece);
        } catch (\RuntimeException $exception) {
            $this->caughtException = $exception;
            $this->occurredEvents = [];
        }
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should (?P<not>not )?be moved (from|to) (?P<coordinates>[a-h][0-8])/
     *
     * @param Piece $piece
     * @param bool $not
     * @param CoordinatePair $coordinates
     */
    public function pieceShouldBeMovedTo(Piece $piece, bool $not, CoordinatePair $coordinates)
    {
        if ($not) {
            expect($this->occurredEvents)->toNotContainEventThatPieceMovedTo($piece, $coordinates);
        } else {
            expect($this->occurredEvents)->toContainEventThatPieceMovedTo($piece, $coordinates);
        }
    }

    /**
     * @Then the :action is/was illegal
     */
    public function actionIsIllegal()
    {
        expect($this->caughtException)->shouldBeAnInstanceOf(IllegalAction::class);
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
    public function kingIsInCheck(Color $color)
    {
        expect($this->occurredEvents)->toContainEvent(new Event\InCheck($color));
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
            'containEventThatPieceMovedTo' => function (array $occurredEvents, Piece $piece, Coordinates $coordinates) {
                foreach ($occurredEvents as $occurredEvent) {
                    if ($occurredEvent instanceof Event\PieceWasMoved && $occurredEvent->piece()->isSameAs($piece) && $occurredEvent->destination()->equals($coordinates)) {
                        return true;
                    }
                }

                return false;
            },
        ];
    }
}
