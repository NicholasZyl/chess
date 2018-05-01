<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Rank;
use PhpSpec\ObjectBehavior;

class PieceSpec extends ObjectBehavior
{
    function it_generates_piece_from_rank_and_color()
    {
        $this->beConstructedThrough(
            'fromRankAndColor',
            [
                Rank::fromString('king'),
                Color::white()
            ]
        );
        $this->shouldHaveType(Piece::class);
    }

    function it_knows_if_is_same_as_another_piece()
    {
        $this->beConstructedThrough(
            'fromRankAndColor',
            [
                Rank::fromString('king'),
                Color::white()
            ]
        );

        $anotherPiece = Piece::fromRankAndColor(
            Rank::fromString('king'),
            Color::white()
        );

        $this->isSameAs($anotherPiece)->shouldBe(true);
    }

    function it_is_different_than_another_piece_if_has_another_color()
    {
        $this->beConstructedThrough(
            'fromRankAndColor',
            [
                Rank::fromString('king'),
                Color::white()
            ]
        );

        $anotherPiece = Piece::fromRankAndColor(
            Rank::fromString('king'),
            Color::black()
        );

        $this->isSameAs($anotherPiece)->shouldBe(false);
    }

    function it_is_different_than_another_piece_if_has_another_rank()
    {
        $this->beConstructedThrough(
            'fromRankAndColor',
            [
                Rank::fromString('king'),
                Color::white()
            ]
        );

        $anotherPiece = Piece::fromRankAndColor(
            Rank::fromString('queen'),
            Color::white()
        );

        $this->isSameAs($anotherPiece)->shouldBe(false);
    }
}
