<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Piece;

final class PieceWasPlaced implements Event
{
    /**
     * @var Piece
     */
    private $piece;

    /**
     * @var Coordinates
     */
    private $at;

    /**
     * Create an event that piece was placed at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $at
     */
    public function __construct(Piece $piece, Coordinates $at)
    {
        $this->piece = $piece;
        $this->at = $at;
    }

    /**
     * What piece was placed.
     *
     * @return Piece
     */
    public function piece(): Piece
    {
        return $this->piece;
    }

    /**
     * Where piece was placed.
     *
     * @return Coordinates
     */
    public function placedAt(): Coordinates
    {
        return $this->at;
    }
}
