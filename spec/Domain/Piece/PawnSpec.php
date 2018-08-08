<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use PhpSpec\ObjectBehavior;

class PawnSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Color::white(),]);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_has_color()
    {
        $this->color()->shouldBeLike(Color::white());
    }

    function it_is_same_as_another_pawn_if_same_color()
    {
        $pawn = Pawn::forColor(Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_is_not_same_as_another_pawn_if_different_color()
    {
        $pawn = Pawn::forColor(Color::black());

        $this->isSameAs($pawn)->shouldBe(false);
    }

    function it_is_not_same_as_another_piece_even_if_same_color(Piece $piece)
    {
        $piece->color()->willReturn(Color::white());

        $this->isSameAs($piece)->shouldBe(false);
    }
}
