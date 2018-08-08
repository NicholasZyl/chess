<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Bishop;
use PhpSpec\ObjectBehavior;

class BishopSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Color::white(),]);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_bishop_if_same_color()
    {
        $pawn = Bishop::forColor(Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }
}
