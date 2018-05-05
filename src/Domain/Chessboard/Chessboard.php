<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Position;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece;

final class Chessboard implements Board
{
    /**
     * @var Position[]
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
    public function placePieceAtCoordinates(Piece $piece, CoordinatePair $coordinates): void
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
    public function verifyThatPositionIsUnoccupied(CoordinatePair $position)
    {
        $this->getSquareAt($position)->verifyThatUnoccupied();
    }

    /**
     * {@inheritdoc}
     */
    public function hasPieceAtCoordinates(Piece $piece, CoordinatePair $coordinates): bool
    {
        return $this->getSquareAt($coordinates)->hasPlacedPiece($piece);
    }

    /**
     * Get square at given coordinates.
     *
     * @param CoordinatePair $coordinates
     *
     * @return Position
     */
    private function getSquareAt(CoordinatePair $coordinates): Position
    {
        return $this->squares[(string)$coordinates];
    }
}
