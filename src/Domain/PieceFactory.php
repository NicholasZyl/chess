<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece\Rook;

class PieceFactory
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
     * Create a piece object from piece's name and color.
     *
     * @param string $rank
     * @param string $color
     *
     * @throws \InvalidArgumentException
     *
     * @return Piece
     */
    public function createPieceNamedForColor(string $rank, string $color): Piece
    {
        if (!array_key_exists($rank, $this->pieceFactories)) {
            throw new \InvalidArgumentException(sprintf('Piece named "%s" does not exist!', $rank));
        }

        return $this->pieceFactories[$rank](Color::fromString($color));
    }

    /**
     * Create a piece object from its description.
     *
     * @param string $pieceDescription
     *
     * @throws \InvalidArgumentException
     *
     * @return Piece
     */
    public function createPieceFromDescription(string $pieceDescription): Piece
    {
        $explodedDescription = explode(' ', $pieceDescription);
        if (count($explodedDescription) !== 2) {
            throw new \InvalidArgumentException(sprintf('Piece description "%s" is missing either rank or color', $pieceDescription));
        }

        return $this->createPieceNamedForColor($explodedDescription[1], $explodedDescription[0]);
    }
}