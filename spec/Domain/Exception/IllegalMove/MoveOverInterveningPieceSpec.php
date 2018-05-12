<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\IllegalMove;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use PhpSpec\ObjectBehavior;

class MoveOverInterveningPieceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            CoordinatePair::fromFileAndRank('b', 2)
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MoveOverInterveningPiece::class);
    }

    function it_is_illegal_move()
    {
        $this->shouldBeAnInstanceOf(IllegalMove::class);
    }

    function it_specifies_the_reason_why_it_is_illegal()
    {
        $this->getMessage()->shouldContain('intervening piece at b2');
    }
}
