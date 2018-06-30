<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\Checkmated;
use NicholasZyl\Chess\Domain\Event\InCheck;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveExposesToCheck;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\KingCheck;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;
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

    function it_is_not_applicable(Board $board, Rules $rules)
    {
        $move = new Move(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('e', 5), CoordinatePair::fromFileAndRank('e', 4));
        $this->shouldThrow(RuleIsNotApplicable::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_does_nothing_if_king_is_not_attacked_after_move(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 8), Color::white(), $rules)->shouldBeCalled()->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('c', 4),
                    CoordinatePair::fromFileAndRank('d', 4)
                )
            ),
            $board,
            $rules
        )->shouldBeLike([]);
    }

    function it_notices_when_opponents_king_is_in_check_after_move(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 8), Color::white(), $rules)->shouldBeCalled()->willReturn(true);

        $board->hasLegalMove(Color::black(), $rules)->shouldBeCalled()->willReturn(true);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('c', 4),
                    CoordinatePair::fromFileAndRank('e', 4)
                )
            ),
            $board,
            $rules
        )->shouldBeLike([new InCheck(Color::black()),]);
    }

    function it_notices_when_opponents_king_is_checkmated_after_move(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 8), Color::white(), $rules)->shouldBeCalled()->willReturn(true);

        $board->hasLegalMove(Color::black(), $rules)->shouldBeCalled()->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('c', 4),
                    CoordinatePair::fromFileAndRank('e', 4)
                )
            ),
            $board,
            $rules
        )->shouldBeLike([new InCheck(Color::black()), new Checkmated(Color::black()),]);
    }

    function it_always_checks_king_actual_position(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('h', 8), Color::white(), $rules)->shouldBeCalled()->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    King::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('e', 8),
                    CoordinatePair::fromFileAndRank('h', 8)
                )
            ),
            $board,
            $rules
        )->shouldBeLike([]);

        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('h', 8), Color::white(), $rules)->shouldBeCalled()->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('c', 4),
                    CoordinatePair::fromFileAndRank('e', 4)
                )
            ),
            $board,
            $rules
        )->shouldBeLike([]);
    }

    function it_notices_when_kings_move_is_exposing_it_to_check(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('f', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(MoveExposesToCheck::class)->during('applyAfter',
            [
                new PieceWasMoved(
                    new Move(
                        King::forColor(Color::white()),
                        CoordinatePair::fromFileAndRank('e', 1),
                        CoordinatePair::fromFileAndRank('f', 1)
                    )
                ),
                $board,
                $rules,
            ]
        );
    }

    function it_notices_when_piece_move_is_exposing_its_king_to_check(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(MoveExposesToCheck::class)->during('applyAfter',
            [
                new PieceWasMoved(
                    new Move(
                        Rook::forColor(Color::white()),
                        CoordinatePair::fromFileAndRank('e', 3),
                        CoordinatePair::fromFileAndRank('f', 3)
                    )
                ),
                $board,
                $rules,
            ]
        );
    }
}
