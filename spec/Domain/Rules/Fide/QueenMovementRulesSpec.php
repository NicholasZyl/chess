<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Fide\QueenMovementRules;
use NicholasZyl\Chess\Domain\Rules\MovementRules;
use PhpSpec\ObjectBehavior;

class QueenMovementRulesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(QueenMovementRules::class);
    }

    function it_is_movement_rule()
    {
        $this->shouldBeAnInstanceOf(MovementRules::class);
    }

    function it_is_for_queen()
    {
        $this->forRank()->shouldBeLike(Rank::queen());
    }

    function it_allows_moving_squares_forward()
    {
        $from = Coordinates::fromString('c3');
        $to = Coordinates::fromString('c8');

        $this->validate($from, $to);
    }

    function it_allows_moving_squares_backward()
    {
        $from = Coordinates::fromString('c6');
        $to = Coordinates::fromString('c1');

        $this->validate($from, $to);
    }

    function it_allows_moving_squares_to_queenside()
    {
        $from = Coordinates::fromString('c4');
        $to = Coordinates::fromString('c2');

        $this->validate($from, $to);
    }

    function it_allows_moving_squares_to_kingside()
    {
        $from = Coordinates::fromString('c4');
        $to = Coordinates::fromString('c7');

        $this->validate($from, $to);
    }

    function it_allows_moving_diagonally_forward_kingside()
    {
        $from = Coordinates::fromString('d4');
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

    function it_disallows_moving_other_directions()
    {
        $from = Coordinates::fromString('d4');
        $to = Coordinates::fromString('c2');

        $this->shouldThrow(new IllegalMove($from, $to))->during('validate', [$from, $to,]);
    }
}
