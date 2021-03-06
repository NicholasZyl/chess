<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Action\Attack;
use NicholasZyl\Chess\Domain\Action\CanMoveCheck;
use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event\Checkmated;
use NicholasZyl\Chess\Domain\Event\InCheck;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Event\Stalemate;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveExposesToCheck;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Rook;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\KingCheck;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class KingCheckSpec extends ObjectBehavior
{
    function let(Board $board)
    {
        $this->beConstructedWith(
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('e', 8)
        );

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(KingCheck::class);
    }

    function it_is_chess_rule()
    {
        $this->shouldBeAnInstanceOf(Rule::class);
    }

    function it_is_not_applicable_to_exchanges()
    {
        $exchange = new Exchange(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('f', 4));
        $this->isApplicableTo($exchange)->shouldBe(false);
    }

    function it_is_applicable_to_move_checks()
    {
        $attack = new CanMoveCheck(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('e', 5), CoordinatePair::fromFileAndRank('e', 4));
        $this->isApplicableTo($attack)->shouldBe(true);
    }

    function it_is_not_applicable_to_attacks()
    {
        $attack = new Attack(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('e', 5), CoordinatePair::fromFileAndRank('e', 4));
        $this->isApplicableTo($attack)->shouldBe(false);
    }

    function it_is_applicable_to_every_move()
    {
        $exchange = new Move(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('e', 5), CoordinatePair::fromFileAndRank('e', 4));
        $this->isApplicableTo($exchange)->shouldBe(true);
    }

    function it_notices_when_opponents_king_is_in_check_after_move(Board $board, Rules $rules)
    {
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

    function it_notices_when_opponent_has_no_valid_move_after_move_and_is_not_checked(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 8), Color::white(), $rules)->shouldBeCalled()->willReturn(false);

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
        )->shouldBeLike([new Stalemate(),]);
    }

    function it_disallows_move_when_kings_move_is_exposing_it_to_check(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('f', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(MoveExposesToCheck::class)->during('apply',
            [
                new Move(
                    King::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('e', 1),
                    CoordinatePair::fromFileAndRank('f', 1)
                ),
                $board,
                $rules,
            ]
        );
    }

    function it_disallows_move_when_piece_move_is_exposing_its_king_to_check(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(MoveExposesToCheck::class)->during('apply',
            [
                new Move(
                    Rook::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('e', 3),
                    CoordinatePair::fromFileAndRank('f', 3)
                ),
                $board,
                $rules,
            ]
        );
    }

    function it_temporarily_removes_captured_piece_for_check(Board $board, Rules $rules)
    {
        $destination = CoordinatePair::fromFileAndRank('f', 3);
        $board->isPositionOccupiedBy($destination, Color::black())->willReturn(true);
        $capturedPiece = Pawn::forColor(Color::black());
        $board->pickPieceFrom($destination)->shouldBeCalled()->willReturn($capturedPiece);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(true);
        $board->placePieceAt($capturedPiece, $destination);

        $this->shouldThrow(MoveExposesToCheck::class)->during('apply',
            [
                new Move(
                    Rook::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('e', 3),
                    $destination
                ),
                $board,
                $rules,
            ]
        );
    }

    function it_traces_kings_moves(Board $board, Rules $rules)
    {
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 8), Color::white(), $rules)->shouldBeCalled()->willReturn(false);

        $board->hasLegalMove(Color::black(), $rules)->shouldBeCalled()->willReturn(true);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    King::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('e', 1),
                    CoordinatePair::fromFileAndRank('g', 3)
                )
            ),
            $board,
            $rules
        );

        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('g', 3), Color::black(), $rules)->shouldBeCalled()->willReturn(false);

        $this->apply(
            new Move(
                Rook::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('e', 3),
                CoordinatePair::fromFileAndRank('f', 3)
            ),
            $board,
            $rules
        );
    }
}
