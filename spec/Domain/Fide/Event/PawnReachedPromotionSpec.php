<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Event;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Event\PawnReachedPromotion;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class PawnReachedPromotionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            Pawn::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 8)
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PawnReachedPromotion::class);
    }

    function it_is_event()
    {
        $this->shouldBeAnInstanceOf(Event::class);
    }

    function it_knows_which_pawn_reached_the_square_of_promotion()
    {
        $this->piece()->shouldBeLike(Pawn::forColor(Color::white()));
    }

    function it_knows_promotion_square_coordinates()
    {
        $this->square()->shouldBeLike(CoordinatePair::fromFileAndRank('a', 8));
    }

    function it_equals_another_event_if_for_same_move()
    {
        $another = new PawnReachedPromotion(
            Pawn::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 8)
        );

        $this->equals($another)->shouldBe(true);
    }

    function it_does_not_equal_another_event_if_for_different_move()
    {
        $another = new PawnReachedPromotion(
            Pawn::forColor(Color::black()),
            CoordinatePair::fromFileAndRank('b', 8)
        );

        $this->equals($another)->shouldBe(false);
    }

    function it_does_not_equal_another_event()
    {
        $another = new Event\PieceWasCaptured(
            Pawn::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 2)
        );

        $this->equals($another)->shouldBe(false);
    }
}
