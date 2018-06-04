<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Chessboard;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;

/**
 * Defines application features from the specific context.
 */
class ChessboardContext implements Context, \PhpSpec\Matcher\MatchersProvider
{
    /**
     * @var \Helper\PieceFactory
     */
    private $pieceFactory;

    /**
     * @var \NicholasZyl\Chess\Domain\Board
     */
    private $chessboard;

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
     * @param \Helper\PieceFactory $pieceFactory
     */
    public function __construct(\Helper\PieceFactory $pieceFactory)
    {
        $this->pieceFactory = $pieceFactory;
    }

    /**
     * @Given there is a chessboard
     */
    public function thereIsAChessboard()
    {
        $this->chessboard = new Chessboard();
    }

    /**
     * @Given /(?P<piece>[a-z]+ [a-z]+) is placed on (?P<coordinates>[a-h][0-8])/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     */
    public function pieceIsPlacedOnSquare(Piece $piece, CoordinatePair $coordinates)
    {
        $this->chessboard->placePieceAtCoordinates($piece, $coordinates);
    }

    /**
     * @Given /there is a chessboard with (?P<piece>[a-z]+ [a-z]+) placed on (?P<coordinates>[a-h][0-8])/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     */
    public function thereIsAChessboardWithPiecePlacedOnSquare(Piece $piece, CoordinatePair $coordinates)
    {
        $this->thereIsAChessboard();
        $this->pieceIsPlacedOnSquare($piece, $coordinates);
    }

    /**
     * @Given following pieces are placed on it
     *
     * @param TableNode $table
     */
    public function followingPiecesArePlacedOnIt(TableNode $table)
    {
        foreach ($table->getHash() as $pieceAtLocation) {
            $this->pieceIsPlacedOnSquare(
                $this->castToPiece($pieceAtLocation['piece']),
                $this->castToCoordinates($pieceAtLocation['location'])
            );
        }
    }

    /**
     * @When I/opponent (tried to) move(d) piece from :source to :destination
     *
     * @param CoordinatePair $source
     * @param CoordinatePair $destination
     */
    public function iMovePieceFromSourceToDestination(CoordinatePair $source, CoordinatePair $destination)
    {
        try {
            $this->chessboard->movePiece($source, $destination);
        } catch (\RuntimeException $exception) {
            $this->caughtException = $exception;
        }
        $this->occurredEvents = $this->chessboard->occurredEvents();
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should be moved to (?P<coordinates>[a-h][0-8])/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     */
    public function pieceShouldBeMovedTo(Piece $piece, CoordinatePair $coordinates)
    {
        expect($this->occurredEvents)->toContainEventThatPieceMovedTo($piece, $coordinates);
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should not be moved from (?P<coordinates>[a-h][0-8])/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     */
    public function pieceShouldNotBeMovedFrom(Piece $piece, CoordinatePair $coordinates)
    {
        expect($this->occurredEvents)->toNotContainEventThatPieceMovedTo($piece, $coordinates);
    }

    /**
     * @Then the move is/was illegal
     */
    public function theMoveIsIllegal()
    {
        expect($this->caughtException)->shouldBeAnInstanceOf(IllegalMove::class);
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) on (?P<coordinates>[a-h][0-8]) should be captured/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     */
    public function pieceOnSquareShouldBeCaptured(Piece $piece, CoordinatePair $coordinates)
    {
        expect($this->occurredEvents)->toContainEventThatPieceWasCaptured($piece, $coordinates);
    }

    /**
     * @Transform :piece
     *
     * @param string $pieceDescription
     * @return Piece
     */
    public function castToPiece(string $pieceDescription)
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
    public function castToCoordinates(string $coordinates)
    {
        return CoordinatePair::fromString($coordinates);
    }

    /**
     * Get helper matcher functions for `expect` function.
     *
     * @return array
     */
    public function getMatchers(): array
    {
        return [
            'containEventThatPieceMovedTo' => function (array $occurredEvents, Piece $piece, Coordinates $coordinates) {
                foreach ($occurredEvents as $occurredEvent) {
                    if ($occurredEvent instanceof Event\PieceWasMoved && $occurredEvent->piece()->isSameAs($piece) && $occurredEvent->destination()->equals($coordinates)) {
                        return true;
                    }
                }

                return false;
                //throw new \PhpSpec\Exception\Example\FailureException(sprintf('Expected that %s moved to %s but it did not.', $piece, $coordinates));
            },
            'containEventThatPieceWasCaptured' => function (array $occurredEvents, Piece $piece, Coordinates $coordinates) {
                foreach ($occurredEvents as $occurredEvent) {
                    if ($occurredEvent instanceof Event\PieceWasCaptured && $occurredEvent->piece()->isSameAs($piece) && $occurredEvent->placedAt()->equals($coordinates)) {
                        return true;
                    }
                }

                return false;
                //throw new \PhpSpec\Exception\Example\FailureException(sprintf('Expected that %s moved to %s but it did not.', $piece, $coordinates));
            },
        ];
    }
}
