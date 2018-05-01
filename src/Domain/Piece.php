<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

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

    private function __construct(Rank $rank, Color $color)
    {
        $this->rank = $rank;
        $this->color = $color;
    }

    public static function fromRankAndColor(Rank $rank, Color $color)
    {
        return new Piece($rank, $color);
    }

    public function isSameAs(Piece $anotherPiece)
    {
        return $this->color->isSameAs($anotherPiece->color) && $this->rank->isSameAs($anotherPiece->rank);
    }
}
