<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Board\Exception\SquareIsVacant;
use NicholasZyl\Chess\Domain\Piece;

final class Square
{
    /**
     * @var Coordinates
     */
    private $coordinates;

    /**
     * @var Piece|null
     */
    private $placedPiece;

    /**
     * Square constructor.
     *
     * @param Coordinates $coordinates
     */
    private function __construct(Coordinates $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * Create square for coordinates.
     *
     * @param Coordinates $coordinates
     * @return Square
     */
    public static function forCoordinates(Coordinates $coordinates): Square
    {
        return new Square($coordinates);
    }

    /**
     * Pick piece from the square.
     *
     * @throws SquareIsVacant
     * @return Piece
     */
    public function pick(): Piece
    {
        if ($this->placedPiece === null) {
            throw new SquareIsVacant($this->coordinates);
        }
        $piece = $this->placedPiece;
        $this->placedPiece = null;

        return $piece;
    }

    /**
     * Place piece on the square.
     *
     * @param Piece $piece
     */
    public function place(Piece $piece): void
    {
        $this->placedPiece = $piece;
    }

    /**
     * Check if same piece is already placed on square.
     *
     * @param Piece $piece
     *
     * @return bool
     */
    public function hasPlacedPiece(Piece $piece): bool
    {
        return $this->placedPiece !== null && $this->placedPiece->isSameAs($piece);
    }
}
