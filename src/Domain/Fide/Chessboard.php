<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Action\Attack;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasExchanged;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\Board\PositionOccupiedByAnotherColor;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Rules;

final class Chessboard implements Board
{
    public const FILE_MOST_QUEENSIDE = 'a';
    public const FILE_MOST_KINGSIDE = 'h';
    public const LOWEST_RANK = 1;
    public const HIGHEST_RANK = 8;

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
        foreach (range(self::FILE_MOST_QUEENSIDE, self::FILE_MOST_KINGSIDE) as $file) {
            foreach (range(self::LOWEST_RANK, self::HIGHEST_RANK) as $rank) {
                $square = Square::forCoordinates(CoordinatePair::fromFileAndRank($file, $rank));
                $this->grid[(string)$square->coordinates()] = $square;
            }
        }
        $this->pieces = [
            Color::WHITE => new \SplObjectStorage(),
            Color::BLACK => new \SplObjectStorage(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function placePieceAt(Piece $piece, Coordinates $position): array
    {
        $events = [];
        $square = $this->getSquareAt($position);
        if ($square->isOccupiedBy($piece->color())) {
            throw new SquareIsOccupied($position);
        }
        $capturedPiece = $square->place($piece);
        $this->pieces[(string)$piece->color()]->attach($piece, $position);
        if ($capturedPiece) {
            $this->pieces[(string)$capturedPiece->color()]->detach($capturedPiece);
            $events[] = new PieceWasCaptured($capturedPiece, $position);
        }

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function pickPieceFrom(Coordinates $position): Piece
    {
        return $this->getSquareAt($position)->pick();
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
    public function isPositionOccupiedBy(Coordinates $position, Color $color): bool
    {
        return $this->getSquareAt($position)->isOccupiedBy($color);
    }

    /**
     * {@inheritdoc}
     */
    public function isPositionAttackedBy(Coordinates $position, Color $color, Rules $rules): bool
    {
        $this->isPositionOnBoard($position);

        $isAttacked = false;
        $piecesSet = $this->pieces[(string)$color];
        foreach ($piecesSet as $piece) {
            $isAttacked = $isAttacked || $this->isAttacking($piece, $piecesSet[$piece], $position, $rules);
        }

        return $isAttacked;
    }

    /**
     * Check if piece is attacking given position.
     *
     * @param Piece $piece
     * @param Coordinates $source
     * @param Coordinates $destination
     * @param Rules $rules
     *
     * @return bool
     */
    private function isAttacking(Piece $piece, Coordinates $source, Coordinates $destination, Rules $rules): bool
    {
        try {
            $rules->applyRulesTo(new Attack($piece, $source, $destination), $this);
        } catch (IllegalAction $illegalAction) {
            return false;
        }

        return true;
    }

    /**
     * Get square at given position.
     *
     * @param Coordinates $position
     *
     * @return Square
     */
    private function getSquareAt(Coordinates $position): Square
    {
        $this->isPositionOnBoard($position);

        return $this->grid[(string)$position];
    }

    /**
     * Check if position is valid for the chessboard.
     *
     * @param Coordinates $position
     *
     * @throws OutOfBoard
     *
     * @return void
     */
    private function isPositionOnBoard(Coordinates $position): void
    {
        if (!array_key_exists((string)$position, $this->grid)) {
            throw new OutOfBoard((string) $position);
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

    /**
     * {@inheritdoc}
     */
    public function exchangePieceOnPositionTo(Coordinates $position, Piece $exchangedPiece): array
    {
        $square = $this->getSquareAt($position);
        if (!$square->isOccupied()) {
            throw new SquareIsUnoccupied($position);
        }
        if (!$square->isOccupiedBy($exchangedPiece->color())) {
            throw new PositionOccupiedByAnotherColor($position, $exchangedPiece->color()->opponent());
        }
        $removedPiece = $square->place($exchangedPiece);
        $this->pieces[(string)$exchangedPiece->color()]->detach($removedPiece);
        $this->pieces[(string)$exchangedPiece->color()]->attach($exchangedPiece);

        return [new PieceWasExchanged($removedPiece, $exchangedPiece, $position),];
    }

    /**
     * Check if given color has any legal move.
     *
     * @param Color $color
     * @param Rules $rules
     *
     * @return bool
     */
    public function hasLegalMove(Color $color, Rules $rules): bool
    {
        $legalMoves = [];
        foreach ($this->pieces[(string)$color] as $piece) {
            $legalMoves = array_merge($legalMoves, $rules->getLegalDestinationsFrom($this->pieces[(string)$color][$piece], $this));
        }

        return !empty($legalMoves);
    }
}
