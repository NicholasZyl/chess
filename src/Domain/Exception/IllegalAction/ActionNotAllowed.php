<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;

final class ActionNotAllowed extends IllegalAction
{
    /**
     * Create an exception that given action is not allowed because of specified reason.
     *
     * @param string $reason
     */
    public function __construct(string $reason)
    {
        parent::__construct(sprintf('Action is not allowed because %s.', $reason));
    }
}
