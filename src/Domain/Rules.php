<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;

class Rules
{
    /**
     * @var Rule[]
     */
    private $rules;

    /**
     * @var PieceMovesRule[]
     */
    private $moveRules = [];

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
            $this->rules[] = $rule;
            if ($rule instanceof PieceMovesRule) {
                if (array_key_exists($rule->isFor(), $this->moveRules)) {
                    throw new \InvalidArgumentException('Rules cannot contain more than one rule for a piece rank.');
                }

                $this->moveRules[$rule->isFor()] = $rule;
            }
        }
    }

    /**
     * Apply rules to given action.
     *
     * @param Action $action
     * @param Board $board
     *
     * @throws IllegalAction
     *
     * @return void
     */
    public function applyRulesTo(Action $action, Board $board): void
    {
        $rules = array_filter(
            $this->rules,
            function (Rule $rule) use ($action) {
                return $rule->isApplicableTo($action);
            }
        );
        if (empty($rules)) {
            throw new NoApplicableRule();
        }

        foreach ($rules as $rule) {
            $rule->apply($action, $board, $this);
        }
    }

    /**
     * Apply rules after event that occurred.
     *
     * @param Event $event
     * @param Board $board
     *
     * @return array
     */
    public function applyAfter(Event $event, Board $board): array
    {
        $events = [];
        foreach ($this->rules as $rule) {
            $events = array_merge($events, $rule->applyAfter($event, $board, $this));
        }

        return $events;
    }

    /**
     * Get all legal destinations for a piece placed on given position.
     *
     * @param Coordinates $position
     * @param Board $board
     *
     * @return Coordinates[]
     */
    public function getLegalDestinationsFrom(Coordinates $position, Board $board): array
    {
        try {
            $piece = $board->pickPieceFrom($position);
            if (!array_key_exists(get_class($piece), $this->moveRules)) {
                throw new NoApplicableRule();
            }

            $destinations = $this->moveRules[get_class($piece)]->getLegalDestinationsFrom($piece, $position, $board);

            $legalDestinations = [];
            foreach ($destinations as $destination) {
                try {
                    $this->applyRulesTo(new Move($piece, $position, $destination), $board);
                    $legalDestinations[] = $destination;
                } catch (IllegalAction $illegalAction) {
                    // Skip
                }
            }

            return $legalDestinations;
        } finally {
            $board->placePieceAt($piece, $position);
        }
    }
}
