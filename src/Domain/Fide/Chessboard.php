<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\OutOfBoardCoordinates;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;

final class Chessboard implements Board
{
    /**
     * @var Square[]
     */
    private $grid = [];

    /**
     * Build the chessboard from passed grid of squares.
     *
     * @param Square[] $grid
     */
    public function __construct(array $grid)
    {
        if (count($grid) !== 64) {
            throw new \InvalidArgumentException('The chessboard must be composed of an 8 x 8 grid of 64 equal squares.');
        }
        foreach ($grid as $square) {
            $this->grid[(string) $square->coordinates()] = $square;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): void
    {
        $this->getSquareAt($coordinates)->place($piece);
    }

    /**
     * {@inheritdoc}
     */
    public function pickPieceFromCoordinates(Coordinates $coordinates): Piece
    {
        return $this->getSquareAt($coordinates)->pick();
    }

    /**
     * {@inheritdoc}
     */
    public function movePiece(Coordinates $source, Coordinates $destination): void
    {
        $from = $this->getSquareAt($source);
        $piece = $from->peek();
        $move = $piece->intentMoveTo($destination);

        try {
            $move->play($this);
        } catch (SquareIsOccupied $squareIsOccupied) {
            throw new MoveToOccupiedPosition($destination);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function verifyThatPositionIsUnoccupied(Coordinates $position)
    {
        $this->getSquareAt($position)->verifyThatUnoccupied();
    }

    /**
     * {@inheritdoc}
     */
    public function hasOpponentsPieceAt(Coordinates $coordinates, Color $pieceColor): bool
    {
        return $this->getSquareAt($coordinates)->hasPlacedOpponentsPiece($pieceColor);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPieceAtCoordinates(Piece $piece, Coordinates $coordinates): bool
    {
        return $this->getSquareAt($coordinates)->hasPlacedPiece($piece);
    }

    /**
     * Get square at given coordinates.
     *
     * @param Coordinates $coordinates
     *
     * @return Square
     */
    private function getSquareAt(Coordinates $coordinates): Square
    {
        if (!array_key_exists((string) $coordinates, $this->grid)) {
            throw new OutOfBoardCoordinates($coordinates);
        }

        return $this->grid[(string)$coordinates];
    }
}
