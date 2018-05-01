<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Fide\KingMovementRules;
use NicholasZyl\Chess\Domain\Rules\MovementRules;
use PhpSpec\ObjectBehavior;

class KingMovementRulesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(KingMovementRules::class);
    }

    function it_is_movement_rule()
    {
        $this->shouldBeAnInstanceOf(MovementRules::class);
    }

    function it_is_for_king()
    {
        $this->isFor()->shouldBeLike(Rank::king());
    }
}
