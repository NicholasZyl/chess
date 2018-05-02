<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\InvalidMove;
use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsNotVacant;
use PhpSpec\ObjectBehavior;

class SquareIsNotVacantSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Coordinates::fromString('a1'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SquareIsNotVacant::class);
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
