<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\CastlingPrevented;
use NicholasZyl\Chess\Domain\Piece\King;
use PhpSpec\ObjectBehavior;

class CastlingPreventedSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            new Move(
                King::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('e', 1),
                CoordinatePair::fromFileAndRank('c', 1)
            )
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CastlingPrevented::class);
    }

    function it_is_illegal_move()
    {
        $this->shouldBeAnInstanceOf(IllegalAction::class);
    }

    function it_specifies_the_reason_why_it_is_illegal()
    {
        $this->getMessage()->shouldContain('is prevented');
    }
}
