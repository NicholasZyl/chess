<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Rules;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Rules;
use NicholasZyl\Chess\Domain\Chessboard\Rules\Exception\IncompleteRules;
use NicholasZyl\Chess\Domain\Chessboard\Rules\Exception\MissingRule;
use NicholasZyl\Chess\Domain\Chessboard\Rules\LawsOfChess;
use NicholasZyl\Chess\Domain\Chessboard\Rules\RankMovementRules;
use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Rank;
use PhpSpec\ObjectBehavior;

class LawsOfChessSpec extends ObjectBehavior
{
    function let(RankMovementRules $kingMovementRules)
    {
        $kingMovementRules->isFor()->shouldBeCalled()->willReturn(Piece\Rank::king());

        $this->beConstructedWith(
            [
                Piece\Rank::king(),
            ],
            [
                $kingMovementRules,
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LawsOfChess::class);
    }

    function it_is_a_rules_set()
    {
        $this->shouldBeAnInstanceOf(Rules::class);
    }

    function it_cannot_be_created_with_missing_ranks_movement_rules(RankMovementRules $kingMovementRules)
    {
        $this->beConstructedWith(
            [
                Piece\Rank::king(),
                Piece\Rank::queen(),
            ],
            [
                $kingMovementRules,
            ]
        );

        $this->shouldThrow(new IncompleteRules([Rank::queen(),]))->duringInstantiation();
    }

    function it_validates_if_given_piece_move_is_legal(RankMovementRules $kingMovementRules)
    {
        $from = Square::forCoordinates(Coordinates::fromString('a1'));
        $from->place(
            Piece::fromRankAndColor(
                Piece\Rank::king(),
                Piece\Color::white()
            )
        );
        $to = Square::forCoordinates(Coordinates::fromString('a2'));

        $kingMovementRules->validate(Coordinates::fromString('a1'), Coordinates::fromString('a2'))->shouldBeCalled();

        $this->validateMove($from, $to);
    }

    function it_fails_if_missing_rule_for_rank_is_not_available()
    {
        $from = Square::forCoordinates(Coordinates::fromString('a1'));
        $from->place(
            Piece::fromRankAndColor(
                Piece\Rank::queen(),
                Piece\Color::white()
            )
        );
        $to = Square::forCoordinates(Coordinates::fromString('a2'));

        $this->shouldThrow(new MissingRule(Rank::queen()))->during('validateMove', [$from, $to,]);
    }
}
