<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\Move\MoveAlongFile;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use PhpSpec\ObjectBehavior;

class MoveAlongFileSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MoveAlongFile::class);
    }

    function it_cannot_be_made_between_coordinates_not_on_the_same_file()
    {
        $from = CoordinatePair::fromFileAndRank('c', 1);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->shouldThrow(new \InvalidArgumentException('c1 and b1 are not along the same file.'))->duringInstantiation();
    }

    function it_is_collection_of_steps_to_make_move_between_coordinates_along_file()
    {
        $from = CoordinatePair::fromFileAndRank('b', 1);
        $to = CoordinatePair::fromFileAndRank('b', 4);
        $this->beConstructedThrough('between', [$from, $to,]);

        $this->count()->shouldBe(3);
        $this->current()->shouldBeLike(CoordinatePair::fromFileAndRank('b', 2));
        $this->next();
        $this->current()->shouldBeLike(CoordinatePair::fromFileAndRank('b', 3));
        $this->next();
        $this->current()->shouldBeLike($to);
    }
}
