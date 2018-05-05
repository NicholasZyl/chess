<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Move\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class MoveNotAllowedForPieceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            AlongRank::between(
                CoordinatePair::fromFileAndRank('a', 3),
                CoordinatePair::fromFileAndRank('c', 3)
            ),
            Pawn::forColor(Color::white())
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MoveNotAllowedForPiece::class);
    }

    function it_is_illegal_move()
    {
        $this->shouldBeAnInstanceOf(IllegalMove::class);
    }

    function it_specifies_coordinates_of_the_move()
    {
        $this->getMessage()->shouldContain('a3');
        $this->getMessage()->shouldContain('c3');
    }

    function it_specifies_the_reason_why_it_is_illegal()
    {
        $this->getMessage()->shouldContain('is not allowed for pawn');
    }
}
