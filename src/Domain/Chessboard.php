<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Square;

final class Chessboard
{
    /**
     * @var Square[]
     */
    private $squares = [];

    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): void
    {
        // TODO: write logic here
    }

    public function movePiece(Coordinates $source, Coordinates $destination): void
    {
        // TODO: write logic here
    }

    public function hasPieceAtCoordinates(Piece $piece, Coordinates $coordinates): bool
    {
        $square = $this->getSquareAt($coordinates);

        return $piece->isSameAs($square->pickPiece());
    }

    private function getSquareAt(Coordinates $coordinates): Square
    {
        return $this->squares[(string) $coordinates];
    }
}
