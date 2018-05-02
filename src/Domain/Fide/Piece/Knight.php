<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Piece;

final class Knight extends Piece
{
    /**
     * @var Piece\Color
     */
    private $color;

    private function __construct(Piece\Color $color)
    {
        $this->color = $color;
    }

    public static function forColor(Piece\Color $color): Knight
    {
        return new Knight($color);
    }

    public function color(): Piece\Color
    {
        return $this->color;
    }

    public function isSameAs(Piece $anotherPiece): bool
    {
        return $anotherPiece instanceof self && $this->color->is($anotherPiece->color);
    }

    public function intentMove(Coordinates $from, Coordinates $to)
    {
        $move = Move::between($from, $to);
        if ($move->isVertical() || $move->isHorizontal() || $move->isDiagonal() || $move->isHigherThan(2)) {
            throw new IllegalMove($from, $to);
        }

        return $move;
    }
}
