<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Board\Square;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SquareSpec extends ObjectBehavior
{
    public function it_is_created_for_chessboard_coordinates()
    {
        $this->beConstructedThrough('forCoordinates', ['A1']);
        $this->shouldHaveType(Square::class);
    }
}
