<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;

abstract class Piece implements \NicholasZyl\Chess\Domain\Piece
{
    /**
     * @var Color
     */
    private $color;

    /**
     * Piece constructor.
     *
     * @param Color $color
     */
    protected function __construct(Color $color)
    {
        $this->color = $color;
    }

    /**
     * Create piece in given color.
     *
     * @param Color $color
     *
     * @return self
     */
    public static function forColor(Color $color): self
    {
        return new static($color);
    }

    /**
     * {@inheritdoc}
     */
    public function color(): Color
    {
        return $this->color;
    }

    /**
     * {@inheritdoc}
     */
    public function hasColor(Color $color): bool
    {
        return $this->color->is($color);
    }

    /**
     * {@inheritdoc}
     */
    public function isSameColorAs(\NicholasZyl\Chess\Domain\Piece $anotherPiece): bool
    {
        return $this->hasColor($anotherPiece->color());
    }

    /**
     * {@inheritdoc}
     */
    public function isSameAs(\NicholasZyl\Chess\Domain\Piece $anotherPiece): bool
    {
        return $anotherPiece instanceof self && $this->isSameColorAs($anotherPiece);
    }

    /**
     * Check that there are no intervening pieces on the board along the move.
     *
     * @param Move $move
     * @param Board $board
     *
     * @throws IllegalMove
     *
     * @return void
     */
    protected function checkForInterveningPieces(Move $move, Board $board): void
    {
        try {
            foreach ($move->steps() as $step) {
                $board->verifyThatPositionIsUnoccupied($step);
            }
        } catch (SquareIsOccupied $squareIsOccupied) {
            throw new MoveOverInterveningPiece($move, $step);
        }
    }
}