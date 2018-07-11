<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece;

final class Square
{
    /**
     * @var CoordinatePair
     */
    private $coordinates;

    /**
     * @var Piece|null
     */
    private $placedPiece;

    /**
     * Square constructor.
     *
     * @param CoordinatePair $coordinates
     */
    private function __construct(CoordinatePair $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * Create square for coordinates.
     *
     * @param CoordinatePair $coordinates
     *
     * @return Square
     */
    public static function forCoordinates(CoordinatePair $coordinates): Square
    {
        return new Square($coordinates);
    }

    /**
     * Get square's coordinates.
     *
     * @return Coordinates
     */
    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    /**
     * Place piece on the square.
     *
     * @param Piece $piece
     *
     * @return Piece|null Returns exchanged piece if any was placed here.
     *
     * @throws SquareIsOccupied
     */
    public function place(Piece $piece): ?Piece
    {
        $capturedPiece = $this->placedPiece;
        $this->placedPiece = $piece;

        return $capturedPiece;
    }

    /**
     * Pick a piece from the square.
     *
     * @throws SquareIsUnoccupied
     *
     * @return Piece
     */
    public function pick(): Piece
    {
        if (!$this->isOccupied()) {
            throw new SquareIsUnoccupied($this->coordinates);
        }
        $piece = $this->placedPiece;
        $this->placedPiece = null;

        return $piece;
    }

    /**
     * Check if there is a piece in given color placed on the square.
     *
     * @param Color $color
     *
     * @return bool
     */
    public function isOccupiedBy(Color $color): bool
    {
        return $this->isOccupied() && $this->placedPiece->hasColor($color);
    }

    /**
     * Is square currently occupied by any piece.
     *
     * @return bool
     */
    public function isOccupied(): bool
    {
        return $this->placedPiece !== null;
    }
}
