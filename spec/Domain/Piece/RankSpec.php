<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Piece;

use NicholasZyl\Chess\Domain\Piece\Rank;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RankSpec extends ObjectBehavior
{
    public function it_can_be_created_from_string()
    {
        $this->beConstructedThrough('fromString', ['king']);
        $this->shouldBeAnInstanceOf(Rank::class);
    }
}
