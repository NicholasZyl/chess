<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use PhpSpec\ObjectBehavior;

class PathSpec extends ObjectBehavior
{
    function it_is_iterable()
    {
        $this->shouldBeAnInstanceOf(\Iterator::class);
    }

    function it_is_collection_of_coordinates_kept_in_correct_order()
    {
        $startingSquare = CoordinatePair::fromFileAndRank('a', 1);
        $interveningSquare = CoordinatePair::fromFileAndRank('a', 2);
        $finalSquare = CoordinatePair::fromFileAndRank('a', 3);

        $this->beConstructedThrough('forSquares', [[$startingSquare, $interveningSquare, $finalSquare,]]);

        $this->current()->shouldBe($startingSquare);
        $this->next();
        $this->current()->shouldBe($interveningSquare);
        $this->next();
        $this->current()->shouldBe($finalSquare);
    }
}
