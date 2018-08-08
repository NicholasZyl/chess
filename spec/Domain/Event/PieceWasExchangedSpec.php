<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\PieceWasExchanged;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Queen;
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

    function it_equals_another_event_if_for_same_exchange()
    {
        $another = new PieceWasExchanged(
            Pawn::forColor(Color::white()),
            Queen::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 8)
        );

        $this->equals($another)->shouldBe(true);
    }

    function it_does_not_equal_another_event_if_for_different_exchange()
    {
        $another = new PieceWasExchanged(
            Pawn::forColor(Color::black()),
            Queen::forColor(Color::black()),
            CoordinatePair::fromFileAndRank('a', 1)
        );

        $this->equals($another)->shouldBe(false);
    }

    function it_does_not_equal_another_event()
    {
        $another = new Event\PieceWasMoved(
            new Move(
                Pawn::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('a', 2),
                CoordinatePair::fromFileAndRank('a', 4)
            )
        );

        $this->equals($another)->shouldBe(false);
    }
}
