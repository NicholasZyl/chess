<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Fide\KnightMovementRules;
use NicholasZyl\Chess\Domain\Rules\MovementRules;
use PhpSpec\ObjectBehavior;

class KnightMovementRulesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(KnightMovementRules::class);
    }

    function it_is_movement_rule()
    {
        $this->shouldBeAnInstanceOf(MovementRules::class);
    }

    function it_is_for_knight()
    {
        $this->isFor()->shouldBeLike(Rank::knight());
    }
}
