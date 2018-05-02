<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\MovementRules;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Fide\KnightMovementRules;
use PhpSpec\ObjectBehavior;

class KnightMovementRulesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(KnightMovementRules::class);
    }

    function it_is_movement_rule()
    {
        $this->shouldBeAnInstanceOf(MovementRules::class);
    }

    function it_is_for_knight()
    {
        $this->forRank()->shouldBeLike(Rank::knight());
    }

    function it_allows_moving_to_the_nearest_coordinate_not_on_same_rank_file_or_diagonal_forwards_queenside()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('c6');

        $this->validate(Color::white(), $from, $to);
    }

    function it_allows_moving_to_the_nearest_coordinate_not_on_same_rank_file_or_diagonal_queenside_forwards()
    {
        $from = Coordinates::fromString('e5');
        $to = Coordinates::fromString('c6');

        $this->validate(Color::black(), $from, $to);
    }

    function it_allows_moving_to_the_nearest_coordinate_not_on_same_rank_file_or_diagonal_forwards_kingside()
    {
        $from = Coordinates::fromString('a1');
        $to = Coordinates::fromString('b3');

        $this->validate(Color::white(), $from, $to);
    }

    function it_allows_moving_to_the_nearest_coordinate_not_on_same_rank_file_or_diagonal_kingside_forwards()
    {
        $from = Coordinates::fromString('b1');
        $to = Coordinates::fromString('d2');

        $this->validate(Color::black(), $from, $to);
    }

    function it_allows_moving_to_the_nearest_coordinate_not_on_same_rank_file_or_diagonal_backwards_queenside()
    {
        $from = Coordinates::fromString('h4');
        $to = Coordinates::fromString('g2');

        $this->validate(Color::white(), $from, $to);
    }

    function it_allows_moving_to_the_nearest_coordinate_not_on_same_rank_file_or_diagonal_queenside_backwards()
    {
        $from = Coordinates::fromString('g7');
        $to = Coordinates::fromString('f5');

        $this->validate(Color::black(), $from, $to);
    }

    function it_allows_moving_to_the_nearest_coordinate_not_on_same_rank_file_or_diagonal_backwards_kingside()
    {
        $from = Coordinates::fromString('c6');
        $to = Coordinates::fromString('d4');

        $this->validate(Color::white(), $from, $to);
    }

    function it_allows_moving_to_the_nearest_coordinate_not_on_same_rank_file_or_diagonal_kingside_backwards()
    {
        $from = Coordinates::fromString('e6');
        $to = Coordinates::fromString('f4');

        $this->validate(Color::black(), $from, $to);
    }

    function it_disallows_moving_vertically()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('f3');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::white(), $from, $to,]);
    }

    function it_disallows_moving_horizontally()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c4');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::black(), $from, $to,]);
    }

    function it_disallows_moving_diagonally()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('d4');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::white(), $from, $to,]);
    }

    function it_disallows_moving_other_directions()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('b2');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::black(), $from, $to,]);
    }

    function it_disallows_moving_further_than_nearest_field()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('f7');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [Color::white(), $from, $to,]);
    }
}
