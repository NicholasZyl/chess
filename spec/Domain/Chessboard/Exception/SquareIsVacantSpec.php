<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\InvalidMove;
use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsVacant;
use PhpSpec\ObjectBehavior;

class SquareIsVacantSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Coordinates::fromString('A1'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SquareIsVacant::class);
    }

    function it_is_invalid_move()
    {
        $this->shouldBeAnInstanceOf(InvalidMove::class);
    }

    function it_describes_coordinates_of_vacant_square()
    {
        $this->getMessage()->shouldContain('a1');
    }
}
