<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Exception\NotPermittedMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Move;
use PhpSpec\ObjectBehavior;

class IllegalMoveSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(CoordinatePair::fromString('A1'), CoordinatePair::fromString('H8'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IllegalMove::class);
    }

    function it_is_invalid_move()
    {
        $this->shouldBeAnInstanceOf(NotPermittedMove::class);
    }

    function it_specifies_which_move_is_illegal()
    {
        $this->getMessage()->shouldContain('a1');
        $this->getMessage()->shouldContain('h8');
    }

    function it_can_be_constructed_for_move(Move $move)
    {
        $move->from()->willReturn(CoordinatePair::fromFileAndRank('a', 1));
        $move->to()->willReturn(CoordinatePair::fromFileAndRank('a', 2));

        $this->beConstructedThrough('forMove', [$move,]);
        $this->getMessage()->shouldContain('a1');
        $this->getMessage()->shouldContain('a2');
    }
}
