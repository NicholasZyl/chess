<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use NicholasZyl\Chess\Domain\Chessboard;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;

/**
 * Defines application features from the specific context.
 */
class ChessboardContext implements Context
{
    /**
     * @var \Helper\PieceFactory
     */
    private $pieceFactory;

    /**
     * @var Chessboard
     */
    private $chessboard;

    /**
     * @var \RuntimeException
     */
    private $caughtException;

    /**
     * @var Chessboard\MoveIntention
     */
    private $moveIntention;

    /**
     * ChessboardContext constructor.
     * @param \Helper\PieceFactory $pieceFactory
     */
    public function __construct(\Helper\PieceFactory $pieceFactory)
    {
        $this->pieceFactory = $pieceFactory;
        $this->moveIntention = new Chessboard\MoveIntention();
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
     * @When I (tried to) move(d) piece from :source to :destination
     *
     * @param CoordinatePair $source
     * @param CoordinatePair $destination
     */
    public function iMovePieceFromSourceToDestination(CoordinatePair $source, CoordinatePair $destination)
    {
        try {
            $this->chessboard->movePiece($this->moveIntention->intentMove($source, $destination));
        } catch (\RuntimeException $exception) {
            $this->caughtException = $exception;
        }
    }

    /**
     * @Then /(?P<piece>[a-z]+ [a-z]+) should (still )?be placed on (?P<coordinates>[a-h][0-8])/
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     */
    public function pieceShouldBePlacedOnSquare(Piece $piece, CoordinatePair $coordinates)
    {
        expect($this->chessboard->hasPieceAtCoordinates($piece, $coordinates))->shouldBe(true);
    }

    /**
     * @Then the move is/was illegal
     */
    public function theMoveIsIllegal()
    {
        expect($this->caughtException)->shouldBeAnInstanceOf(Chessboard\Exception\IllegalMove::class);
    }

    /**
     * @Then the move is not permitted
     */
    public function theMoveIsNotPermitted()
    {
        expect($this->caughtException)->shouldBeAnInstanceOf(Chessboard\Exception\NotPermittedMove::class);
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
}
