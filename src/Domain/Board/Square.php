<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Piece;

final class Square
{
    /**
     * @var Piece|null
     */
    private $placedPiece;

    private function __construct()
    {
    }

    public static function forCoordinates(Coordinates $coordinates)
    {
        $square = new Square();

        // TODO: write logic here

        return $square;
    }

    public function pick(): Piece
    {
        return $this->placedPiece;
    }

    public function place(Piece $piece): void
    {
        $this->placedPiece = $piece;
    }
}
