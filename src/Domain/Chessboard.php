<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard\Exception\NotPermittedMove;
use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;

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
                $coordinates = Coordinates::fromFileAndRank($file, $rank);
                $this->squares[(string)$coordinates] = Square::forCoordinates($coordinates);
            }
        }
    }

    /**
     * Place a piece on square at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $coordinates
     *
     * @return void
     */
    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): void
    {
        $this->getSquareAt($coordinates)->place($piece);
    }

    /**
     * Move a piece from one coordinate to another.
     *
     * @param Coordinates $source
     * @param Coordinates $destination
     *
     * @throws NotPermittedMove
     *
     * @return void
     */
    public function movePiece(Coordinates $source, Coordinates $destination): void
    {
        $from = $this->getSquareAt($source);
        $to = $this->getSquareAt($destination);
        $piece = $from->pick();

        try {
            $piece->intentMove($source, $destination);
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
     * @param Coordinates $coordinates
     *
     * @return bool
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
