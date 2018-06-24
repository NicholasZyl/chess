<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;

interface Rule
{
    public const STANDARD_PRIORITY = 10;
    public const HIGH_PRIORITY = 50;

    /**
     * Get the priority of rule. The higher means more important.
     *
     * @return int
     */
    public function priority(): int;

    /**
     * Apply rule after event had happened. May return more events.
     *
     * @param Event $event
     * @param Game $game
     *
     * @return Event[]
     */
    public function applyAfter(Event $event, Game $game): array;

    /**
     * Is rule applicable for given action.
     *
     * @param Action $action
     *
     * @return bool
     */
    public function isApplicable(Action $action): bool;

    /**
     * Apply rule to the action.
     *
     * @param Action $action
     * @param Game $game
     *
     * @throws IllegalAction
     *
     * @return void
     */
    public function apply(Action $action, Game $game): void;
}
