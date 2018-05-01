<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Piece\Rank;
use PhpSpec\ObjectBehavior;

class RankSpec extends ObjectBehavior
{
    function it_can_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['king']);
        $this->shouldBeAnInstanceOf(Rank::class);
    }

    function it_is_the_same_as_another_rank()
    {
        $this->beConstructedThrough('fromString', ['king']);
        $anotherRank = Rank::fromString('king');

        $this->isSameAs($anotherRank)->shouldBe(true);
    }

    function it_is_different_if_has_different_rank()
    {
        $this->beConstructedThrough('fromString', ['king']);
        $anotherRank = Rank::fromString('queen');

        $this->isSameAs($anotherRank)->shouldBe(false);
    }
}
