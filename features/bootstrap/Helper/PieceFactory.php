<?php
declare(strict_types=1);

namespace Helper;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece\Rook;

final class PieceFactory
{
    /**
     * @var callable[]
     */
    private $pieceFactories;

    /**
     * PieceFactory constructor.
     */
    public function __construct()
    {
        $this->pieceFactories = [
            'pawn' => function (Color $color) {
                return Pawn::forColor($color);
            },
            'knight' => function (Color $color) {
                return Knight::forColor($color);
            },
            'bishop' => function (Color $color) {
                return Bishop::forColor($color);
            },
            'rook' => function (Color $color) {
                return Rook::forColor($color);
            },
            'queen' => function (Color $color) {
                return Queen::forColor($color);
            },
            'king' => function (Color $color) {
                return King::forColor($color);
            },
        ];
    }

    /**
     * Helper for creating piece object from piece's name.
     *
     * @param string $name
     * @param Color $color
     *
     * @throws \InvalidArgumentException
     *
     * @return Piece
     */
    public function createPieceNamedForColor(string $name, Color $color): Piece
    {
        if (!array_key_exists($name, $this->pieceFactories)) {
            throw new \InvalidArgumentException(sprintf('Piece named "%s" does not exist!', $name));
        }

        return $this->pieceFactories[$name]($color);
    }
}