<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Exception\SquareIsVacant;
use NicholasZyl\Chess\Domain\Board\Square;

final class Chessboard
{
    /**
     * @var Square[]
     */
    private $squares = [];

    public function __construct()
    {
        foreach (range('a', 'h') as $file) {
            foreach (range(1, 8) as $rank) {
                $coordinates = Coordinates::fromFileAndRank($file, $rank);
                $this->squares[(string)$coordinates] = Square::forCoordinates($coordinates);
            }
        }
    }

    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): void
    {
        $this->getSquareAt($coordinates)->place($piece);
    }

    public function movePiece(Coordinates $source, Coordinates $destination): void
    {
        $piece = $this->getSquareAt($source)->pick();
        $this->getSquareAt($destination)->place($piece);
    }

    public function hasPieceAtCoordinates(Piece $piece, Coordinates $coordinates): bool
    {
        try {
            return $this->getSquareAt($coordinates)->hasPlacedPiece($piece);
        } catch (SquareIsVacant $squareIsVacant) {
            return false;
        }
    }

    private function getSquareAt(Coordinates $coordinates): Square
    {
        return $this->squares[(string)$coordinates];
    }
}
