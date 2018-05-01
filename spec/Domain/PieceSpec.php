<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Rank;
use PhpSpec\ObjectBehavior;

class PieceSpec extends ObjectBehavior
{
    public function it_generates_piece_from_rank_and_color()
    {
        $this->beConstructedThrough(
            'fromRankAndColor',
            [
                Rank::fromString('king'),
                Color::fromString('white')
            ]
        );
        $this->shouldHaveType(Piece::class);
    }

    public function it_knows_if_is_same_as_another_piece()
    {
        $this->beConstructedThrough(
            'fromRankAndColor',
            [
                Rank::fromString('king'),
                Color::fromString('white')
            ]
        );

        $anotherPiece = Piece::fromRankAndColor(
            Rank::fromString('king'),
            Color::fromString('white')
        );

        $this->isSameAs($anotherPiece)->shouldBe(true);
    }
}
