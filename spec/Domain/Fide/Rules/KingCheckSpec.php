<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Event\InCheck;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\KingCheck;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rule;
use PhpSpec\ObjectBehavior;

class KingCheckSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(KingCheck::class);
    }

    function it_is_chess_rule()
    {
        $this->shouldBeAnInstanceOf(Rule::class);
    }

    function it_has_standard_priority()
    {
        $this->priority()->shouldBe(10);
    }

    function it_is_never_applicable()
    {
        $exchange = new Exchange(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('f', 4));
        $this->isApplicable($exchange)->shouldBe(false);
    }

    function it_is_not_applicable(Game $game)
    {
        $move = new Move(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('e', 5), CoordinatePair::fromFileAndRank('e', 4));
        $this->shouldThrow(RuleIsNotApplicable::class)->during('apply', [$move, $game,]);
    }

    function it_does_nothing_if_king_is_not_attacked_after_move(Game $game)
    {
        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('e', 8), Color::black())->shouldBeCalled()->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('c', 4),
                    CoordinatePair::fromFileAndRank('d', 4)
                )
            ),
            $game
        )->shouldBeLike([]);
    }

    function it_notices_when_opponents_king_is_in_check_after_move(Game $game)
    {
        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('e', 8), Color::black())->shouldBeCalled()->willReturn(true);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('c', 4),
                    CoordinatePair::fromFileAndRank('e', 4)
                )
            ),
            $game
        )->shouldBeLike([new InCheck(Color::black()),]);
    }

    function it_validates_kings_actual_position(Game $game)
    {
        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('e', 1), Color::white())->shouldBeCalled()->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    King::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('e', 8),
                    CoordinatePair::fromFileAndRank('h', 8)
                )
            ),
            $game
        )->shouldBeLike([]);

        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('h', 8), Color::black())->shouldBeCalled()->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('c', 4),
                    CoordinatePair::fromFileAndRank('e', 4)
                )
            ),
            $game
        )->shouldBeLike([]);
    }
}
