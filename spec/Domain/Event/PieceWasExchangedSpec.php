<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\PieceWasExchanged;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class PieceWasExchangedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            Pawn::forColor(Color::white()),
            Queen::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 8)
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PieceWasExchanged::class);
    }

    function it_is_event()
    {
        $this->shouldBeAnInstanceOf(Event::class);
    }

    function it_knows_what_piece_was_exchanged()
    {
        $this->piece()->shouldBeLike(Pawn::forColor(Color::white()));
    }

    function it_knows_with_which_piece_was_exchanged()
    {
        $this->exchangedWith()->shouldBeLike(Queen::forColor(Color::white()));
    }

    function it_knows_the_position_where_piece_was_exchanged()
    {
        $this->position()->shouldBeLike(CoordinatePair::fromFileAndRank('a', 8));
    }
}
