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
        foreach (range('a', 'h') as $file) {
            foreach (range(1, 8) as $rank) {
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
     * Move a piece from one coordinate to another.
     *
     * @param CoordinatePair $source
     * @param CoordinatePair $destination
     *
     * @throws NotPermittedMove
     *
     * @return void
     */
    public function movePiece(CoordinatePair $source, CoordinatePair $destination): void
    {
        $from = $this->getSquareAt($source);
        $to = $this->getSquareAt($destination);
        $piece = $from->pick();

        try {
            $move = $piece->intentMove($source, $destination);
            foreach ($move->path() as $stepCoordinates) {
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
