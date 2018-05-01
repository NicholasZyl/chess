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

    private function __construct(Coordinates $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    public static function forCoordinates(Coordinates $coordinates): Square
    {
        return new Square($coordinates);
    }

    public function pick(): Piece
    {
        $piece = $this->getPlacedPiece();
        $this->placedPiece = null;

        return $piece;
    }

    public function place(Piece $piece): void
    {
        $this->placedPiece = $piece;
    }

    public function hasPlacedPiece(Piece $piece): bool
    {
        $placedPiece = $this->getPlacedPiece();

        return $placedPiece->isSameAs($piece);
    }

    private function getPlacedPiece(): Piece
    {
        if ($this->placedPiece === null) {
            throw new SquareIsVacant($this->coordinates);
        }

        return $this->placedPiece;
    }
}
