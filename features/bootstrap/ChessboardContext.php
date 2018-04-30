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
}
