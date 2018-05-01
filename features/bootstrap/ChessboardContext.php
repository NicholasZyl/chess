<?php
declare(strict_types=1);

use Behat\Behat\Context\Context;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
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
     * @Given there is a chessboard with :piece placed on :coordinates
     */
    public function thereIsAChessboardWithPiecePlacedOnSquare(Piece $piece, Coordinates $coordinates)
    {
        $this->chessboard = new Chessboard();
        $this->chessboard->placePieceAtCoordinates($piece, $coordinates);
    }

    /**
     * @When I move piece from :source to :destination
     */
    public function iMovePieceFromSourceToDestination(Coordinates $source, Coordinates $destination)
    {
        $this->chessboard->movePiece($source, $destination);
    }

    /**
     * @Then :piece should be placed on :coordinates
     */
    public function pieceShouldBePlacedOnSquare(Piece $piece, Coordinates $coordinates)
    {
        expect($this->chessboard->hasPieceAtCoordinates($piece, $coordinates))->shouldBe(true);
    }

    /**
     * @Transform :piece
     */
    public function castToPiece(string $pieceDescription)
    {
        $pieceDescription = explode(' ', $pieceDescription);
        if (count($pieceDescription) !== 2) {
            throw new \InvalidArgumentException(sprintf('Piece description "%s" is missing either rank or color'));
        }

        return Piece::fromRankAndColor(Rank::fromString($pieceDescription[0]), Color::fromString($pieceDescription[1]));
    }

    /**
     * @Transform :coordinates
     * @Transform :source
     * @Transform :destination
     */
    public function castToCoordinates(string $coordinates)
    {
        return Coordinates::fromString($coordinates);
    }
}
