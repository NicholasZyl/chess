<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Exception\InvalidMove;
use PhpSpec\ObjectBehavior;

class IllegalMoveSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Coordinates::fromString('A1'), Coordinates::fromString('H8'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(IllegalMove::class);
    }

    function it_is_invalid_move()
    {
        $this->shouldBeAnInstanceOf(InvalidMove::class);
    }

    function it_specifies_which_move_is_illegal()
    {
        $this->getMessage()->shouldContain('a1');
        $this->getMessage()->shouldContain('h8');
    }
}
