<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

interface Event extends \JsonSerializable
{
    /**
     * Check if event is same as another one.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function equals(Event $event): bool;
}