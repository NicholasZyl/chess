<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Piece\Rank;
use PhpSpec\ObjectBehavior;

class RankSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('fromString', ['king']);
    }

    function it_can_be_created_from_string()
    {
        $this->shouldBeAnInstanceOf(Rank::class);
    }

    function it_is_the_same_as_another_rank()
    {
        $anotherRank = Rank::king();

        $this->isSameAs($anotherRank)->shouldBe(true);
    }

    function it_is_different_if_has_different_rank()
    {
        $anotherRank = Rank::queen();

        $this->isSameAs($anotherRank)->shouldBe(false);
    }

    function it_has_string_representation()
    {
        $this->__toString()->shouldBe('king');
    }

    function it_cannot_be_created_for_unknown_rank()
    {
        $this->beConstructedThrough('fromString', ['Unknown']);

        $this->shouldThrow(new \InvalidArgumentException('"unknown" is not a valid piece rank.'))->duringInstantiation();
    }
}
