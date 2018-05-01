<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Fide\BishopMovementRules;
use NicholasZyl\Chess\Domain\Rules\MovementRules;
use PhpSpec\ObjectBehavior;

class BishopMovementRulesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BishopMovementRules::class);
    }

    function it_is_movement_rule()
    {
        $this->shouldBeAnInstanceOf(MovementRules::class);
    }

    function it_is_for_bishop()
    {
        $this->isFor()->shouldBeLike(Rank::bishop());
    }

    function it_allows_moving_diagonally_forward_kingside()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('f6');

        $this->validate($from, $to);
    }

    function it_allows_moving_diagonally_forward_queenside()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('a7');

        $this->validate($from, $to);
    }

    function it_allows_moving_diagonally_backward_kingside()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('e3');

        $this->validate($from, $to);
    }

    function it_allows_moving_diagonally_backward_queenside()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('b2');

        $this->validate($from, $to);
    }

    function it_disallows_moving_vertically()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('f3');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [$from, $to,]);
    }

    function it_disallows_moving_horizontally()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c4');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [$from, $to,]);
    }

    function it_disallows_moving_in_other_directions()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('e4');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [$from, $to,]);
    }
}
