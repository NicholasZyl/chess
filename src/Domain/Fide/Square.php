<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
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
     * @return Event[]
     *
     * @throws SquareIsOccupied
     */
    public function place(Piece $piece): array
    {
        if ($this->placedPiece !== null && $this->placedPiece->isSameColorAs($piece)) {
            throw new SquareIsOccupied($this->coordinates);
        }
        $events = [];
        if ($this->hasPlacedOpponentsPiece($piece->color())) {
            $events []= new Event\PieceWasCaptured($this->placedPiece, $this->coordinates);
        }

        $this->placedPiece = $piece;
        $piece->placeAt($this->coordinates);

        return $events;
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
        if ($this->placedPiece === null) {
            throw new SquareIsUnoccupied($this->coordinates);
        }

        return $this->placedPiece;
    }

    /**
     * Verify that the position is unoccupied by any piece.
     *
     * @throws SquareIsOccupied
     *
     * @return void
     */
    public function verifyThatUnoccupied(): void
    {
        if ($this->placedPiece !== null) {
            throw new SquareIsOccupied($this->coordinates);
        }
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
        return $this->placedPiece !== null && !$this->placedPiece->hasColor($color);
    }
}
