<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Exception\Board\PositionOccupiedByAnotherColor;
use NicholasZyl\Chess\Domain\Exception\BoardException;
use PhpSpec\ObjectBehavior;

class PositionOccupiedByAnotherColorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(CoordinatePair::fromFileAndRank('b', 3), Color::white());
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType(PositionOccupiedByAnotherColor::class);
    }

    function it_is_board_exception()
    {
        $this->shouldBeAnInstanceOf(BoardException::class);
    }

    function it_knows_coordinates_of_occupied_square()
    {
        $this->coordinates()->shouldBeLike(CoordinatePair::fromFileAndRank('b', 3));
    }

    function it_knows_color_which_occupies_the_square()
    {
        $this->color()->shouldBeLike(Color::white());
    }
}
