<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
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
     *
     * @return Square
     */
    public static function forCoordinates(Coordinates $coordinates): Square
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
     * @throws SquareIsOccupied
     */
    public function place(Piece $piece): void
    {
        if ($this->placedPiece !== null) {
            throw new SquareIsOccupied($this->coordinates);
        }

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

    /**
     * Pick piece from the square.
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
        if ($this->placedPiece === null) {
            throw new SquareIsUnoccupied($this->coordinates);
        }

        return $this->placedPiece;
    }
}
