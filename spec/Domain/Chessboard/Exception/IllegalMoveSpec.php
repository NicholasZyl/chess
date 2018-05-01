<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
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

    function it_is_runtime_exception()
    {
        $this->shouldBeAnInstanceOf(\RuntimeException::class);
    }

    function it_specifies_which_move_is_illegal()
    {
        $this->getMessage()->shouldContain('a1');
        $this->getMessage()->shouldContain('h8');
    }
}
