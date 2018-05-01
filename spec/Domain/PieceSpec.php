<?php

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PieceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Piece::class);
    }
}
