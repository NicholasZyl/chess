<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;

final class GameEnded implements Event
{
    /**
     * @var Color|null
     */
    private $winner;

    /**
     * Create an event for the completion of the game.
     *
     * @param Color|null $winner
     */
    public function __construct(?Color $winner = null)
    {
        $this->winner = $winner;
    }

    /**
     * Which color won the game.
     *
     * @return Color|null
     */
    public function winner(): ?Color
    {
        return $this->winner;
    }

    /**
     * Does event equal another one.
     *
     * @param Event|null $anotherEvent
     *
     * @return bool
     */
    public function equals(?Event $anotherEvent): bool
    {
        return $anotherEvent instanceof self
            && ((!$this->winner && !$anotherEvent->winner())
            || ($this->winner && $anotherEvent->winner() && $this->winner->is($anotherEvent->winner())));
    }
}
