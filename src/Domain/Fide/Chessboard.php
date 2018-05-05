<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece;

final class Chessboard implements Board
{
    /**
     * @var Square[]
     */
    private $squares = [];

    /**
     * Chessboard constructor.
     * Initialise the full chessboard with all squares on it. Initially empty.
     */
    public function __construct()
    {
        foreach (CoordinatePair::validFiles() as $file) {
            foreach (CoordinatePair::validRanks() as $rank) {
                $coordinates = CoordinatePair::fromFileAndRank($file, $rank);
                $this->squares[(string)$coordinates] = Square::forCoordinates($coordinates);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): void
    {
        $this->getSquareAt($coordinates)->place($piece);
    }

    /**
     * {@inheritdoc}
     */
    public function movePiece(Move $move): void
    {
        $from = $this->getSquareAt($move->from());
        $to = $this->getSquareAt($move->to());
        $piece = $from->pick();

        try {
            $piece->mayMove($move, $this);
            $to->place($piece);
        } catch (IllegalMove $invalidMove) {
            $from->place($piece);
            throw $invalidMove;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function verifyThatPositionIsUnoccupied(Coordinates $position)
    {
        $this->getSquareAt($position)->verifyThatUnoccupied();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPieceAtCoordinates(Piece $piece, Coordinates $coordinates): bool
    {
        return $this->getSquareAt($coordinates)->hasPlacedPiece($piece);
    }

    /**
     * Get square at given coordinates.
     *
     * @param Coordinates $coordinates
     *
     * @return Square
     */
    private function getSquareAt(Coordinates $coordinates): Square
    {
        return $this->squares[(string)$coordinates];
    }
}
