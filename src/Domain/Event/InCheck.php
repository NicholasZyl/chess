<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;

final class InCheck implements Event
{
    /**
     * @var Color
     */
    private $color;

    /**
     * Create an event that color is in check.
     *
     * @param Color $color
     */
    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    /**
     * Which color is in check.
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
        return $anotherEvent instanceof self && $this->color->is($anotherEvent->color());
    }
}
