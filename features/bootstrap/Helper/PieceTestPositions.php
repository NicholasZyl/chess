<?php
declare(strict_types=1);

namespace Helper;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Piece;

class PieceTestPositions implements Piece\InitialPositions
{
    /**
     * @var \SplObjectStorage|Coordinates[]|Piece[]
     */
    private $initialPositions;

    /**
     * Initialise board setup for test purposes.
     */
    public function __construct()
    {
        $this->initialPositions = new \SplObjectStorage();
    }

    /**
     * Plan placing piece at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $at
     *
     * @return void
     */
    public function placePieceAt(Piece $piece, Coordinates $at): void
    {
        $this->initialPositions->attach($at, $piece);
    }

    /**
     * {@inheritdoc}
     */
    public function initialiseBoard(Board $board): void
    {
        foreach ($this->initialPositions as $at) {
            $board->placePieceAtCoordinates($this->initialPositions[$at], $at);
        }
    }
}