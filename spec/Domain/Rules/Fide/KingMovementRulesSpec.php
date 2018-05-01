<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
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

    function it_allows_moving_one_square_forward()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c4');

        $this->validate($from, $to);
    }

    function it_disallows_moving_more_than_one_rank()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c5');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [$from, $to,]);
    }

    function it_disallows_moving_more_than_one_file()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('e3');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [$from, $to,]);
    }
}