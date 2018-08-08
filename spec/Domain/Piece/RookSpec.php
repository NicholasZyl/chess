<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Rook;
use PhpSpec\ObjectBehavior;

class RookSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Color::white(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Rook::class);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_rook_if_same_color()
    {
        $pawn = Rook::forColor(Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }
}
