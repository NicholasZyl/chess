<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use NicholasZyl\Chess\Domain\Chessboard;
use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\Rank;

/**
 * Defines application features from the specific context.
 */
class ChessboardContext implements Context
{
    /**
     * @var Chessboard
     */
    private $chessboard;

    /**
     * @var \RuntimeException
     */
    private $caughtException;

    /**
     * @Given there is a chessboard with :piece placed on :coordinates
     *
     * @param Piece $piece
     * @param Coordinates $coordinates
     */
    public function thereIsAChessboardWithPiecePlacedOnSquare(Piece $piece, Coordinates $coordinates)
    {
        $this->chessboard = new Chessboard(new class implements \NicholasZyl\Chess\Domain\Chessboard\Rules {

            public function validateMove(Square $from, Square $to): void
            {
                // Naive implementation
            }
        });
        $this->chessboard->placePieceAtCoordinates($piece, $coordinates);
    }

    /**
     * @When I move piece from :source to :destination
     *
     * @param Coordinates $source
     * @param Coordinates $destination
     */
    public function iMovePieceFromSourceToDestination(Coordinates $source, Coordinates $destination)
    {
        try {
            $this->chessboard->movePiece($source, $destination);
        } catch (\RuntimeException $exception) {
            $this->caughtException = $exception;
        }
    }

    /**
     * @Then :piece should be placed on :coordinates
     * @Then :piece should still be placed on :coordinates
     *
     * @param Piece $piece
     * @param Coordinates $coordinates
     */
    public function pieceShouldBePlacedOnSquare(Piece $piece, Coordinates $coordinates)
    {
        expect($this->chessboard->hasPieceAtCoordinates($piece, $coordinates))->shouldBe(true);
    }

    /**
     * @Then the move is illegal
     */
    public function theMoveIsIllegal()
    {
        expect($this->caughtException)->shouldBeAnInstanceOf(IllegalMove::class);
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

        return Piece::fromRankAndColor(Rank::fromString($pieceDescription[1]), Color::fromString($pieceDescription[0]));
    }

    /**
     * @Transform :coordinates
     * @Transform :source
     * @Transform :destination
     *
     * @param string $coordinates
     * @return Coordinates
     */
    public function castToCoordinates(string $coordinates)
    {
        return Coordinates::fromString($coordinates);
    }
}
