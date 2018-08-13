<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Action;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\CanMoveCheck;
use NicholasZyl\Chess\Domain\Action\Movement;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use PhpSpec\ObjectBehavior;

class CanMoveCheckSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 2), CoordinatePair::fromFileAndRank('c', 3));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CanMoveCheck::class);
    }

    function it_is_game_action()
    {
        $this->shouldBeAnInstanceOf(Action::class);
    }

    function it_is_piece_movement_action()
    {
        $this->shouldBeAnInstanceOf(Movement::class);
    }
}
