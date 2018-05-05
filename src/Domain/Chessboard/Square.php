<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Board\Position;

final class Square implements Position
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
     * @return Position
     */
    public static function forCoordinates(CoordinatePair $coordinates): Position
    {
        return new Square($coordinates);
    }

    /**
     * {@inheritdoc}
     */
    public function place(Piece $piece): void
    {
        $this->verifyThatUnoccupied();

        $this->placedPiece = $piece;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPlacedPiece(Piece $piece): bool
    {
        return $this->placedPiece !== null && $this->placedPiece->isSameAs($piece);
    }

    /**
     * {@inheritdoc}
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
    private function peek(): Piece
    {
        if ($this->placedPiece === null) {
            throw new SquareIsUnoccupied($this->coordinates);
        }

        return $this->placedPiece;
    }

    /**
     * {@inheritdoc}
     */
    public function verifyThatUnoccupied(): void
    {
        if ($this->placedPiece !== null) {
            throw new SquareIsOccupied($this->coordinates);
        }
    }
}
