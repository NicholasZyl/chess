<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoardCoordinates;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules;

final class Chessboard implements Board
{
    private const LOWEST_FILE = 'a';
    private const HIGHEST_FILE = 'h';
    private const LOWEST_RANK = 1;
    private const HIGHEST_RANK = 8;

    /**
     * @var Rules
     */
    private $rules;

    /**
     * @var Square[]
     */
    private $grid = [];

    /**
     * @var Event[]
     */
    private $occurredEvents = [];

    /**
     * Build the chessboard as a 8 x 8 grid of 64 squares.
     *
     * @param Rules $rules
     */
    public function __construct(Rules $rules)
    {
        foreach (range(self::LOWEST_FILE, self::HIGHEST_FILE) as $file) {
            foreach (range(self::LOWEST_RANK, self::HIGHEST_RANK) as $rank) {
                $square = Square::forCoordinates(CoordinatePair::fromFileAndRank($file, $rank));
                $this->grid[(string)$square->coordinates()] = $square;
            }
        }
        $this->rules = $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): void
    {
        $events = $this->getSquareAt($coordinates)->place($piece);
        $this->occurredEvents = array_merge($this->occurredEvents, $events);
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
        $events = $move->play($this, $this->rules);
        $this->occurredEvents = array_merge($this->occurredEvents, $events);
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
    public function isPositionAttackedByOpponentOf(Coordinates $coordinates, Color $color): bool
    {
        $this->areCoordinatesOnBoard($coordinates);

        foreach ($this->grid as $square) {
            if ($square->hasPlacedOpponentsPiece($color) && $square->peek()->isAttacking($coordinates, $this)) {
                return true;
            }
        }

        return false;
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
    public function occurredEvents(): array
    {
        return $this->occurredEvents;
    }
}
