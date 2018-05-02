<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Piece;

final class Pawn extends Piece
{
    /**
     * @var Piece\Color
     */
    private $color;

    private $firstMove = true;

    private function __construct(Piece\Color $color)
    {
        $this->color = $color;
    }

    public static function forColor(Piece\Color $color): Pawn
    {
        return new Pawn($color);
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
        if (!$move->isForward($this->color) || $move->isHigherThan($this->firstMove ? 2 : 1) || !$move->isVertical()) {
            throw new IllegalMove($from, $to);
        }
        $this->firstMove = false;

        return $move;
    }


}
