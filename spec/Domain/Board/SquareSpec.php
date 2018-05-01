<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Square;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class SquareSpec extends ObjectBehavior
{
    function let()
    {
        $coordinates = Coordinates::fromString('A1');
        $this->beConstructedThrough('forCoordinates', [$coordinates]);
    }

    public function it_is_created_for_chessboard_coordinates()
    {
        $this->shouldHaveType(Square::class);
    }

    public function it_allows_to_pick_piece_placed_on_it()
    {
        $this->pickPiece()->shouldBeAnInstanceOf(Piece::class);
    }
}
