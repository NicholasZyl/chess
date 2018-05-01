<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Exception\IncompleteRules;
use NicholasZyl\Chess\Domain\Rules\Exception\MissingRule;
use NicholasZyl\Chess\Domain\Rules\Fide\BishopMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\KingMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\KnightMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\PawnMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\QueenMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\RookMovementRules;
use NicholasZyl\Chess\Domain\Rules\MovementRules;
use PhpSpec\ObjectBehavior;

class LawsOfChessSpec extends ObjectBehavior
{
    function let(MovementRules $kingMovementRules)
    {
        $kingMovementRules->forRank()->willReturn(Piece\Rank::king());

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

    function it_cannot_be_created_with_missing_ranks_movement_rules(MovementRules $kingMovementRules)
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

    function it_validates_if_given_piece_move_is_legal(MovementRules $kingMovementRules)
    {
        $from = Square::forCoordinates(Coordinates::fromString('a1'));
        $from->place(
            Piece::fromRankAndColor(
                Piece\Rank::king(),
                Piece\Color::white()
            )
        );
        $to = Square::forCoordinates(Coordinates::fromString('a2'));

        $kingMovementRules->validate(Piece\Color::white(), Coordinates::fromString('a1'), Coordinates::fromString('a2'))->shouldBeCalled();

        $this->validateMove($from, $to);
    }

    function it_fails_if_rule_for_rank_is_not_available()
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

    function it_lists_all_movement_rules_inside_fide_laws()
    {
        $this->beConstructedThrough('fromFideHandbook');

        $this->listMovementRules()->shouldHaveCount(6);
        $this->listMovementRules()->shouldBeLike(
            [
                new KingMovementRules(),
                new QueenMovementRules(),
                new RookMovementRules(),
                new BishopMovementRules(),
                new KnightMovementRules(),
                new PawnMovementRules(),
            ]
        );
    }
}
