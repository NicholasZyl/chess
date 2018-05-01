<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\Rank;

class Piece
{
    /**
     * @var Rank
     */
    private $rank;

    /**
     * @var Color
     */
    private $color;

    /**
     * Piece constructor.
     *
     * @param Rank $rank
     * @param Color $color
     */
    private function __construct(Rank $rank, Color $color)
    {
        $this->rank = $rank;
        $this->color = $color;
    }

    /**
     * Create a piece with given rank and color.
     *
     * @param Rank $rank
     * @param Color $color
     *
     * @return Piece
     */
    public static function fromRankAndColor(Rank $rank, Color $color)
    {
        return new Piece($rank, $color);
    }

    /**
     * Compare if piece has the same rank and color as another one.
     *
     * @param Piece $anotherPiece
     *
     * @return bool
     */
    public function isSameAs(Piece $anotherPiece)
    {
        return $this->color->isSameAs($anotherPiece->color) && $this->rank->isSameAs($anotherPiece->rank);
    }
}
