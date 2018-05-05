<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Move\AlongFile;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
use PhpSpec\ObjectBehavior;

class MoveToOccupiedPositionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            AlongFile::between(
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('a', 3)
            ),
            CoordinatePair::fromFileAndRank('a', 3)
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MoveToOccupiedPosition::class);
    }

    function it_is_illegal_move()
    {
        $this->shouldBeAnInstanceOf(IllegalMove::class);
    }

    function it_specifies_coordinates_of_the_move()
    {
        $this->getMessage()->shouldContain('a1');
        $this->getMessage()->shouldContain('a3');
    }

    function it_specifies_the_reason_why_it_is_illegal()
    {
        $this->getMessage()->shouldContain('a3 is occupied by a piece of same color');
    }
}
