<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard\Exception\NotPermittedMove;
use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class Chessboard
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
     * Place a piece on square at given coordinates.
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     *
     * @return void
     */
    public function placePieceAtCoordinates(Piece $piece, CoordinatePair $coordinates): void
    {
        $this->getSquareAt($coordinates)->place($piece);
    }

    /**
     * Move a piece from one square to another.
     *
     * @param Move $move
     *
     * @throws NotPermittedMove
     *
     * @return void
     */
    public function movePiece(Move $move): void
    {
        $from = $this->getSquareAt($move->from());
        $to = $this->getSquareAt($move->to());
        $piece = $from->pick();

        try {
            $piece->mayMove($move);
            foreach ($move as $stepCoordinates) {
                $this->getSquareAt($stepCoordinates)->verifyThatUnoccupied();
            }
            $to->place($piece);
        } catch (NotPermittedMove $invalidMove) {
            $from->place($piece);
            throw $invalidMove;
        }
    }

    /**
     * Check if same piece is already placed on square at given coordinates.
     *
     * @param Piece $piece
     * @param CoordinatePair $coordinates
     *
     * @return bool
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
     * @return Square
     */
    private function getSquareAt(CoordinatePair $coordinates): Square
    {
        return $this->squares[(string)$coordinates];
    }
}
