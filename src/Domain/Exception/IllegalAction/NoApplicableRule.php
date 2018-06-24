<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;

final class NoApplicableRule extends IllegalAction
{
    /**
     * Create an exception that there is no applicable rule for an action.
     */
    public function __construct()
    {
        parent::__construct('There is no applicable rule for an action.');
    }
}
