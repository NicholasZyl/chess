<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use PhpSpec\ObjectBehavior;

class MoveToIllegalPositionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            new Move(
                Pawn::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('a', 2),
                CoordinatePair::fromFileAndRank('b', 2)
            )
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IllegalAction\MoveToIllegalPosition::class);
    }

    function it_is_illegal_move()
    {
        $this->shouldBeAnInstanceOf(IllegalAction::class);
    }

    function it_specifies_the_reason_why_it_is_illegal()
    {
        $this->getMessage()->shouldContain('is illegal position for pawn');
    }
}
