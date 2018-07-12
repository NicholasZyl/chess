<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Action;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Attack;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use PhpSpec\ObjectBehavior;

class AttackSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 2), CoordinatePair::fromFileAndRank('c', 3));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Attack::class);
    }

    function it_is_game_action()
    {
        $this->shouldBeAnInstanceOf(Action::class);
    }

    function it_is_kind_of_piece_move()
    {
        $this->shouldBeAnInstanceOf(Move::class);
    }
}
