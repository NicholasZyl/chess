<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RulesSpec extends ObjectBehavior
{
    function let(Rule $firstRule, Rule $secondRule)
    {
        $this->beConstructedWith([$firstRule, $secondRule,]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Rules::class);
    }

    function it_cannot_contain_non_rules(Rule $firstRule)
    {
        $this->beConstructedWith([$firstRule, new class {},]);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_applies_applicable_rules_to_action(Rule $firstRule, Rule $secondRule, Game $game)
    {
        $action = new class implements Action {};

        $firstRule->isApplicable($action)->shouldBeCalled()->willReturn(true);
        $secondRule->isApplicable($action)->shouldBeCalled()->willReturn(false);
        $firstRule->apply($action, $game)->shouldBeCalled();
        $secondRule->apply(Argument::cetera())->shouldNotBeCalled();

        $this->applyRulesTo($action, $game);
    }

    function it_fails_if_there_is_no_applicable_rule_to_action(Rule $firstRule, Rule $secondRule, Game $game)
    {
        $action = new class implements Action {};

        $firstRule->isApplicable($action)->shouldBeCalled()->willReturn(false);
        $secondRule->isApplicable($action)->shouldBeCalled()->willReturn(false);
        $firstRule->apply(Argument::cetera())->shouldNotBeCalled();
        $secondRule->apply(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(NoApplicableRule::class)->during('applyRulesTo', [$action, $game,]);
    }

    function it_applies_rules_to_occurred_event(Rule $firstRule, Rule $secondRule, Game $game)
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

        $firstRule->applyAfter($event, $game)->shouldBeCalled()->willReturn([$otherEvent,]);
        $secondRule->applyAfter($event, $game)->shouldBeCalled()->willReturn([]);

        $this->applyAfter($event, $game)->shouldBe([$otherEvent,]);
    }

//    function it_applies_only_most_important_applicable_rule(Board $board, InitialPositions $initialPositions, Rule $rule, Rule $lessImportantRule)
//    {
//        $this->beConstructedWith($board, $initialPositions, [$lessImportantRule, $rule,]);
//
//        $pawn = Pawn::forColor(Color::white());
//        $source = CoordinatePair::fromFileAndRank('c', 2);
//        $destination = CoordinatePair::fromFileAndRank('c', 3);
//        $move = new Move($pawn, $source, $destination);
//
//        $lessImportantRule->isApplicable($move)->shouldBeCalled()->willReturn(true);
//        $lessImportantRule->apply($move, $this->getWrappedObject())->shouldNotBeCalled();
//        $lessImportantRule->priority()->willReturn(10);
//
//        $rule->isApplicable($move)->shouldBeCalled()->willReturn(true);
//        $rule->apply($move, $this->getWrappedObject())->shouldBeCalled();
//        $rule->priority()->willReturn(50);
//
//        $board->pickPieceFrom($source)->shouldBeCalled()->willReturn($pawn);
//        $board->placePieceAt($pawn, $destination)->shouldBeCalled()->willReturn([]);
//
//        $rule->applyAfter(Argument::cetera())->shouldBeCalled();
//        $lessImportantRule->applyAfter(Argument::cetera())->shouldBeCalled();
//
//        $this->playMove($source, $destination)->shouldBeLike([new PieceWasMoved($move),]);
//    }
}
