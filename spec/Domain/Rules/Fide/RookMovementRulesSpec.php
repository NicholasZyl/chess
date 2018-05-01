<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Fide\RookMovementRules;
use NicholasZyl\Chess\Domain\Rules\MovementRules;
use PhpSpec\ObjectBehavior;

class RookMovementRulesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RookMovementRules::class);
    }

    function it_is_movement_rule()
    {
        $this->shouldBeAnInstanceOf(MovementRules::class);
    }

    function it_is_for_rook()
    {
        $this->isFor()->shouldBeLike(Rank::rook());
    }
}
