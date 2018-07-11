<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;

final class Checkmated implements Event
{
    /**
     * @var Color
     */
    private $color;

    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    /**
     * Get color that's checkmated.
     *
     * @return Color
     */
    public function color(): Color
    {
        return $this->color;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(?Event $anotherEvent): bool
    {
        return $anotherEvent instanceof self && $anotherEvent->color()->is($this->color);
    }
}
