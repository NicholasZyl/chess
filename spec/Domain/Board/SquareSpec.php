<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Square;
use PhpSpec\ObjectBehavior;

class SquareSpec extends ObjectBehavior
{
    public function it_is_created_for_chessboard_coordinates()
    {
        $coordinates = Coordinates::fromString('A1');
        $this->beConstructedThrough('forCoordinates', [$coordinates]);
        $this->shouldHaveType(Square::class);
    }
}
