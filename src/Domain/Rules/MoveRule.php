<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Move;

interface MoveRule
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
     * Is rule applicable for given move.
     *
     * @param Move $move
     *
     * @return bool
     */
    public function isApplicable(Move $move): bool;

    /**
     * Apply rules to the move.
     *
     * @param Move $move
     * @param Game $game
     *
     * @throws IllegalAction
     *
     * @return void
     */
    public function apply(Move $move, Game $game): void;
}
