<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasPlaced;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoardCoordinates;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;

final class Chessboard implements Board
{
    private const LOWEST_FILE = 'a';
    private const HIGHEST_FILE = 'h';
    private const LOWEST_RANK = 1;
    private const HIGHEST_RANK = 8;

    /**
     * @var Square[]
     */
    private $grid = [];

    /**
     * @var \SplObjectStorage[]|Piece[][]|Coordinates[][]
     */
    private $pieces;

    /**
     * Build the chessboard as a 8 x 8 grid of 64 squares.
     */
    public function __construct()
    {
        foreach (range(self::LOWEST_FILE, self::HIGHEST_FILE) as $file) {
            foreach (range(self::LOWEST_RANK, self::HIGHEST_RANK) as $rank) {
                $square = Square::forCoordinates(CoordinatePair::fromFileAndRank($file, $rank));
                $this->grid[(string)$square->coordinates()] = $square;
            }
        }
        $this->pieces = [
            (string) Color::white() => new \SplObjectStorage(),
            (string) Color::black() => new \SplObjectStorage(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): array
    {
        $events = [];
        $capturedPiece = $this->getSquareAt($coordinates)->place($piece);
        $events[] = new PieceWasPlaced($piece, $coordinates);
        $this->pieces[(string)$piece->color()]->attach($piece, $coordinates);
        if ($capturedPiece) {
            $this->pieces[(string)$capturedPiece->color()]->detach($capturedPiece);
            $events[] = new PieceWasCaptured($capturedPiece, $coordinates);
        }

        return $events;
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
    public function isPositionOccupied(Coordinates $position): bool
    {
        return $this->getSquareAt($position)->isOccupied();
    }

    /**
     * {@inheritdoc}
     */
    public function isPositionOccupiedByOpponentOf(Coordinates $coordinates, Color $pieceColor): bool
    {
        return $this->getSquareAt($coordinates)->hasPlacedOpponentsPiece($pieceColor);
    }

    /**
     * {@inheritdoc}
     */
    public function isPositionAttackedBy(Coordinates $position, Color $color, Game $game): bool
    {
        $this->areCoordinatesOnBoard($position);

        $isAttacked = false;
        $piecesSet = $this->pieces[(string)$color];
        foreach ($piecesSet as $piece) {
            $isAttacked = $isAttacked || $game->mayMove($piece, $piecesSet[$piece], $position);
        }

        return $isAttacked;
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
        $this->areCoordinatesOnBoard($coordinates);

        return $this->grid[(string)$coordinates];
    }

    /**
     * Check if coordinates are valid for the chessboard.
     *
     * @param Coordinates $coordinates
     *
     * @throws OutOfBoardCoordinates
     *
     * @return void
     */
    private function areCoordinatesOnBoard(Coordinates $coordinates): void
    {
        if (!array_key_exists((string)$coordinates, $this->grid)) {
            throw new OutOfBoardCoordinates($coordinates);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removePieceFrom(Coordinates $position): Piece
    {
        $piece = $this->getSquareAt($position)->pick();
        $this->pieces[(string)$piece->color()]->detach($piece);

        return $piece;
    }
}
