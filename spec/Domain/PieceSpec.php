<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\Rank;
use PhpSpec\ObjectBehavior;

class PieceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough(
            'fromRankAndColor',
            [
                Rank::fromString('king'),
                Color::white()
            ]
        );
    }

    function it_knows_if_is_same_as_another_piece()
    {
        $anotherPiece = Piece::fromRankAndColor(
            Rank::fromString('king'),
            Color::white()
        );

        $this->isSameAs($anotherPiece)->shouldBe(true);
    }

    function it_is_different_than_another_piece_if_has_another_color()
    {
        $anotherPiece = Piece::fromRankAndColor(
            Rank::fromString('king'),
            Color::black()
        );

        $this->isSameAs($anotherPiece)->shouldBe(false);
    }

    function it_is_different_than_another_piece_if_has_another_rank()
    {
        $anotherPiece = Piece::fromRankAndColor(
            Rank::fromString('queen'),
            Color::white()
        );

        $this->isSameAs($anotherPiece)->shouldBe(false);
    }

    function it_has_rank()
    {
        $this->rank()->shouldBeLike(Rank::fromString('king'));
    }
}
