<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Square;

final class Chessboard
{
    /**
     * @var Rules
     */
    private $rules;

    /**
     * @var Square[]
     */
    private $squares = [];

    /**
     * Chessboard constructor.
     * Initialise the full chessboard with all squares on it. Initially empty.
     *
     * @param Rules $rules
     */
    public function __construct(Rules $rules)
    {
        $this->rules = $rules;

        foreach (range('a', 'h') as $file) {
            foreach (range(1, 8) as $rank) {
                $coordinates = Coordinates::fromFileAndRank($file, $rank);
                $this->squares[(string)$coordinates] = Square::forCoordinates($coordinates);
            }
        }
    }

    /**
     * Place a piece on square at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $coordinates
     *
     * @return void
     */
    public function placePieceAtCoordinates(Piece $piece, Coordinates $coordinates): void
    {
        $this->getSquareAt($coordinates)->place($piece);
    }

    /**
     * Move a piece from one coordinate to another.
     *
     * @param Coordinates $source
     * @param Coordinates $destination
     *
     * @return void
     */
    public function movePiece(Coordinates $source, Coordinates $destination): void
    {
        $from = $this->getSquareAt($source);
        $to = $this->getSquareAt($destination);
        $this->makeMove($from, $to);
    }

    /**
     * Make a move from one square to another.
     *
     * @param Square $from
     * @param Square $to
     *
     * @return void
     */
    private function makeMove(Square $from, Square $to): void
    {
        $this->rules->validateMove($from, $to);
        $piece = $from->pick();
        $to->place($piece);
    }

    /**
     * Check if same piece is already placed on square at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $coordinates
     *
     * @return bool
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
        return $this->squares[(string)$coordinates];
    }
}
