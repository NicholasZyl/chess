<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

interface Event
{
    /**
     * Does event equal another one.
     *
     * @param Event|null $anotherEvent
     *
     * @return bool
     */
    public function equals(?Event $anotherEvent): bool;
}