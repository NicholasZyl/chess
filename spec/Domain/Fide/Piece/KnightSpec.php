<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class KnightSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Color::white(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Knight::class);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_knight_if_same_color()
    {
        $pawn = Knight::forColor(Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }
}
