<?php
declare(strict_types=1);

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;

/**
 * Defines application features from the specific context.
 */
class ChessboardContext implements Context
{
    /**
     * @Given there is a chessboard with :piece placed on :square
     */
    public function thereIsAChessboardWithPiecePlacedOnSquare(Piece $piece, Square $square)
    {
        throw new PendingException();
    }

    /**
     * @When I move piece from :source to :destination
     */
    public function iMovePieceFromSourceToDestination(Square $source, Square $destination)
    {
        throw new PendingException();
    }

    /**
     * @Then :piece should be placed on :square
     */
    public function pieceShouldBePlacedOnSquare()
    {
        throw new PendingException();
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
}
