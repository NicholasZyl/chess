<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Action;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Piece;

final class Exchange implements Action
{
    /**
     * @var Piece
     */
    private $pieceToExchangeWith;

    /**
     * @var Coordinates
     */
    private $position;

    /**
     * Create an exchange action to happen on given position.
     *
     * @param Piece $pieceToExchangeWith
     * @param Coordinates $position
     */
    public function __construct(Piece $pieceToExchangeWith, Coordinates $position)
    {
        $this->pieceToExchangeWith = $pieceToExchangeWith;
        $this->position = $position;
    }

    /**
     * Where the exchange should happen.
     *
     * @return Coordinates
     */
    public function position(): Coordinates
    {
        return $this->position;
    }

    /**
     * With which piece exchange should happen.
     *
     * @return Piece
     */
    public function pieceToExchangeWith(): Piece
    {
        return $this->pieceToExchangeWith;
    }
}
