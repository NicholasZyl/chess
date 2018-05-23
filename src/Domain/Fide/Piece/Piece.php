<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
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
     * {@inheritdoc}
     */
    public function isAttacking(Coordinates $coordinates, Board $board): bool
    {
        try {
            $move = $this->intentMoveTo($coordinates);
            $this->mayMove($move, $board);
            $move->isLegal($board);

            return true;
        } catch (IllegalMove $illegalMove) {
            return false;
        }
    }
}