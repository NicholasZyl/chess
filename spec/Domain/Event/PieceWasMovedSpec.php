<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class PieceWasMovedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            new Move(
                Pawn::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('a', 2),
                CoordinatePair::fromFileAndRank('a', 4)
            )
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PieceWasMoved::class);
    }

    function it_is_event()
    {
        $this->shouldBeAnInstanceOf(Event::class);
    }

    function it_knows_what_piece_was_placed()
    {
        $this->piece()->shouldBeLike(Pawn::forColor(Color::white()));
    }

    function it_knows_which_coordinates_piece_was_placed_at_before_move()
    {
        $this->source()->shouldBeLike(CoordinatePair::fromFileAndRank('a', 2));
    }

    function it_knows_which_coordinates_piece_was_placed_at_after_move()
    {
        $this->destination()->shouldBeLike(CoordinatePair::fromFileAndRank('a', 4));
    }

    function it_knows_when_was_over_given_distance()
    {
        $this->wasOverDistanceOf(2)->shouldBe(true);
    }

    function it_knows_when_was_not_over_given_distance()
    {
        $this->wasOverDistanceOf(3)->shouldBe(false);
    }
}
