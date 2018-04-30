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
     * @Given there is a chessboard with White king placed on D4
     */
    public function thereIsAChessboardWithWhiteKingPlacedOnD()
    {
        throw new PendingException();
    }

    /**
     * @When I move piece from D4 to E5
     */
    public function iMovePieceFromDToE()
    {
        throw new PendingException();
    }

    /**
     * @Then White king should be placed on E5
     */
    public function whiteKingShouldBePlacedOnE()
    {
        throw new PendingException();
    }
}
