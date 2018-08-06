<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveExposesToCheck;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\BishopMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\Turns;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RulesSpec extends ObjectBehavior
{
    function let(PieceMovesRule $firstRule, PieceMovesRule $secondRule, Rule $otherRule)
    {
        $this->beConstructedWith([$firstRule, $secondRule, $otherRule,]);

        $firstRule->isFor()->willReturn(Pawn::class);
        $secondRule->isFor()->willReturn(Rook::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Rules::class);
    }

    function it_cannot_contain_non_rules(PieceMovesRule $firstRule)
    {
        $this->beConstructedWith([$firstRule, new class {},]);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_cannot_contain_more_than_one_movement_rule_for_given_piece_rank(PieceMovesRule $firstRule, PieceMovesRule $secondRule)
    {
        $this->beConstructedWith([$firstRule, $secondRule,]);

        $firstRule->isFor()->willReturn('\Chess\Some\Piece\Rank');
        $secondRule->isFor()->willReturn('\Chess\Some\Piece\Rank');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_applies_all_applicable_rules_to_action(PieceMovesRule $firstRule, PieceMovesRule $secondRule, Rule $otherRule, Board $board)
    {
        $action = new class implements Action {
            /**
             * {@inheritdoc}
             */
            public function player(): Color
            {
                return Color::white();
            }
        };

        $firstRule->isApplicableTo($action)->shouldBeCalled()->willReturn(true);
        $secondRule->isApplicableTo($action)->shouldBeCalled()->willReturn(true);
        $otherRule->isApplicableTo($action)->shouldBeCalled()->willReturn(true);
        $firstRule->apply($action, $board, $this->getWrappedObject())->shouldBeCalled();
        $secondRule->apply($action, $board, $this->getWrappedObject())->shouldBeCalled();
        $otherRule->apply($action, $board, $this->getWrappedObject())->shouldBeCalled();

        $this->applyRulesTo($action, $board);
    }

    function it_fails_if_there_is_no_applicable_rule_to_action(PieceMovesRule $firstRule, PieceMovesRule $secondRule, Rule $otherRule, Board $board)
    {
        $action = new class implements Action {
            /**
             * {@inheritdoc}
             */
            public function player(): Color
            {
                return Color::white();
            }
        };

        $firstRule->isApplicableTo($action)->shouldBeCalled()->willReturn(false);
        $secondRule->isApplicableTo($action)->shouldBeCalled()->willReturn(false);
        $otherRule->isApplicableTo($action)->shouldBeCalled()->willReturn(false);
        $firstRule->apply(Argument::cetera())->shouldNotBeCalled();
        $secondRule->apply(Argument::cetera())->shouldNotBeCalled();
        $otherRule->apply(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(NoApplicableRule::class)->during('applyRulesTo', [$action, $board,]);
    }

    function it_applies_rules_to_occurred_event(PieceMovesRule $firstRule, PieceMovesRule $secondRule, Rule $otherRule, Board $board)
    {
        $event = new class implements Event {
            /**
             * {@inheritdoc}
             */
            public function equals(?Event $anotherEvent): bool
            {
                return false;
            }
        };
        $otherEvent = new class implements Event {
            /**
             * {@inheritdoc}
             */
            public function equals(?Event $anotherEvent): bool
            {
                return false;
            }
        };

        $firstRule->applyAfter($event, $board, $this->getWrappedObject())->shouldBeCalled()->willReturn([$otherEvent,]);
        $secondRule->applyAfter($event, $board, $this->getWrappedObject())->shouldBeCalled()->willReturn([]);
        $otherRule->applyAfter($event, $board, $this->getWrappedObject())->shouldBeCalled()->willReturn([]);

        $this->applyAfter($event, $board)->shouldBe([$otherEvent,]);
    }

    function it_gets_all_currently_available_destinations_for_a_piece_placed_at_position_according_to_applicable_rule(PieceMovesRule $firstRule, PieceMovesRule $secondRule, Rule $otherRule, Board $board)
    {
        $piece = Rook::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 1);

        $board->pickPieceFrom($source)->shouldBeCalled()->willReturn($piece);

        $firstRule->isApplicableTo(Argument::type(Action\Move::class))->shouldBeCalled()->willReturn(false);
        $firstRule->apply(Argument::cetera())->shouldNotBeCalled();

        $legalDestination = CoordinatePair::fromFileAndRank('a', 2);
        $illegalDestination = CoordinatePair::fromFileAndRank('b', 3);
        $secondRule->getLegalDestinationsFrom($piece, $source, $board)->shouldBeCalled()->will(function () use ($legalDestination, $illegalDestination) {
            yield $legalDestination;
            yield $illegalDestination;
        });

        $moveToLegalDestination = new Action\Move($piece, $source, $legalDestination);
        $secondRule->isApplicableTo($moveToLegalDestination)->shouldBeCalled()->willReturn(true);
        $secondRule->apply($moveToLegalDestination, $board, $this->getWrappedObject())->shouldBeCalled();

        $moveToIllegalDestination = new Action\Move($piece, $source, $illegalDestination);
        $secondRule->isApplicableTo($moveToIllegalDestination)->shouldBeCalled()->willReturn(true);
        $secondRule->apply($moveToIllegalDestination, $board, $this->getWrappedObject())->shouldBeCalled();

        $otherRule->isApplicableTo($moveToLegalDestination)->shouldBeCalled()->willReturn(true);
        $otherRule->isApplicableTo($moveToIllegalDestination)->shouldBeCalled()->willReturn(true);
        $otherRule->apply($moveToLegalDestination, $board, $this->getWrappedObject())->shouldBeCalled();
        $otherRule->apply($moveToIllegalDestination, $board, $this->getWrappedObject())->shouldBeCalled()->willThrow(MoveExposesToCheck::class);

        $board->placePieceAt($piece, $source)->shouldBeCalled();

        $this->getLegalDestinationsFrom($source, $board)->shouldBeLike([$legalDestination,]);
    }

    function it_fails_if_there_is_no_applicable_rule_to_get_legal_moves_for_a_piece(Board $board)
    {
        $piece = Knight::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $board->pickPieceFrom($source)->shouldBeCalled()->willReturn($piece);
        $board->placePieceAt($piece, $source)->shouldBeCalled();

        $this->shouldThrow(NoApplicableRule::class)->during('getLegalDestinationsFrom', [$source, $board,]);
    }

    function it_fails_if_there_is_no_piece_at_position(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $board->pickPieceFrom($source)->shouldBeCalled()->willThrow(SquareIsUnoccupied::class);

        $this->shouldThrow(SquareIsUnoccupied::class)->during('getLegalDestinationsFrom', [$source, $board,]);
    }

    function it_allows_to_replace_rule()
    {
        $rule = new Turns();
        $replacedRule = new Turns(Color::black());
        $nonReplacedRule = new BishopMoves();

        $this->beConstructedWith([$nonReplacedRule, $rule,]);

        $this->replace([$replacedRule,])->shouldBeLike(new Rules([$nonReplacedRule, $replacedRule,]));
    }
}
