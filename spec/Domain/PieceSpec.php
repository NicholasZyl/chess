<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Color;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PieceSpec extends ObjectBehavior
{
    public function it_generates_piece_from_rank_and_color()
    {
        $this->beConstructedThrough(
            'fromRankAndColor',
            [
                Rank::fromString('king'),
                Color::fromString('white')
            ]
        );
        $this->shouldHaveType(Piece::class);
    }
}
