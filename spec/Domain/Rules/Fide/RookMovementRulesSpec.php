<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\MovementRules;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Fide\RookMovementRules;
use PhpSpec\ObjectBehavior;

class RookMovementRulesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RookMovementRules::class);
    }

    function it_is_movement_rule()
    {
        $this->shouldBeAnInstanceOf(MovementRules::class);
    }

    function it_is_for_rook()
    {
        $this->forRank()->shouldBeLike(Rank::rook());
    }

    function it_allows_moving_vertically_forward()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c7');

        $this->validate(Color::white(), $from, $to);
    }

    function it_allows_moving_vertically_backward()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c2');

        $this->validate(Color::black(), $from, $to);
    }

    function it_allows_moving_horizontally_queenside()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('a3');

        $this->validate(Color::white(), $from, $to);
    }

    function it_allows_moving_horizontally_kingside()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('d3');

        $this->validate(Color::black(), $from, $to);
    }

    function it_disallows_moving_diagonally()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('d4');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::white(), $from, $to,]);
    }

    function it_disallows_moving_in_other_directions()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('e4');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::black(), $from, $to,]);
    }
}
