<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\CastlingPrevented;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
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
