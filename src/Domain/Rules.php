<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;

class Rules
{
    /**
     * @var Rule[]
     */
    private $rules;

    /**
     * Create a set of rules.
     *
     * @param Rule[] $rules
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $rule) {
            if (!$rule instanceof Rule) {
                throw new \InvalidArgumentException('Rules can consists only of rule objects.');
            }
        }
        $this->rules = $rules;
    }

    /**
     * Apply rules to given action.
     *
     * @param Action $action
     * @param Game $game
     *
     * @throws IllegalAction
     *
     * @return void
     */
    public function applyRulesTo(Action $action, Game $game): void
    {
        $rules = array_filter(
            $this->rules,
            function (Rule $rule) use ($action) {
                return $rule->isApplicable($action);
            }
        );
        if (empty($rules)) {
            throw new NoApplicableRule();
        }
        usort($rules, function (Rule $ruleA, Rule $ruleB) {
            return $ruleB->priority() <=> $ruleA->priority();
        });

        /** @var Rule $rule */
        $rule = reset($rules);
        $rule->apply($action, $game);
    }

    /**
     * Apply rules after event that occurred.
     *
     * @param Event $event
     * @param Game $game
     *
     * @return array
     */
    public function applyAfter(Event $event, Game $game): array
    {
        $events = [];
        foreach ($this->rules as $rule) {
            $events = array_merge($events, $rule->applyAfter($event, $game));
        }

        return $events;
    }
}
