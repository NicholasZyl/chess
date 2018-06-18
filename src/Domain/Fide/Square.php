<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board\Coordinates;
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
     * @return Piece|null Returns captured piece if was placed here.
     *
     * @throws SquareIsOccupied
     */
    public function place(Piece $piece): ?Piece
    {
        if ($this->isOccupied() && $this->placedPiece->isSameColorAs($piece)) {
            throw new SquareIsOccupied($this->coordinates);
        }

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
        $piece = $this->peek();
        $this->placedPiece = null;

        return $piece;
    }

    /**
     * Peek what piece is placed on the square.
     *
     * @throws SquareIsUnoccupied
     *
     * @return Piece
     */
    public function peek(): Piece
    {
        if (!$this->isOccupied()) {
            throw new SquareIsUnoccupied($this->coordinates);
        }

        return $this->placedPiece;
    }

    /**
     * Check if there is a placed piece and the piece has different color.
     *
     * @param Piece\Color $color
     *
     * @return bool
     */
    public function hasPlacedOpponentsPiece(Piece\Color $color): bool
    {
        return $this->isOccupied() && !$this->placedPiece->hasColor($color);
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
