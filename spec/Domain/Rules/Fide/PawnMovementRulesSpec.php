<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Fide\PawnMovementRules;
use NicholasZyl\Chess\Domain\Rules\MovementRules;
use PhpSpec\ObjectBehavior;

class PawnMovementRulesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PawnMovementRules::class);
    }

    function it_is_movement_rule()
    {
        $this->shouldBeAnInstanceOf(MovementRules::class);
    }

    function it_is_for_pawn()
    {
        $this->forRank()->shouldBeLike(Rank::pawn());
    }

    function it_allows_moving_one_square_vertically_forward()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c4');

        $this->validate(Color::white(), $from, $to);
    }

    function it_disallows_moving_vertically_more_then_one_square()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c5');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::white(), $from, $to,]);
    }

    function it_disallows_moving_horizontally_queenside()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('b3');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::black(), $from, $to,]);
    }

    function it_disallows_moving_horizontally_kingside()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('d3');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::white(), $from, $to,]);
    }

    function it_disallows_moving_diagonally()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('d4');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::black(), $from, $to,]);
    }

    function it_disallows_moving_other_directions()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('b2');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::white(), $from, $to,]);
    }

    function it_disallows_moving_backward_for_whites()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c2');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::white(), $from, $to,]);
    }

    function it_disallows_moving_backward_for_blacks()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c4');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::black(), $from, $to,]);
    }
}
