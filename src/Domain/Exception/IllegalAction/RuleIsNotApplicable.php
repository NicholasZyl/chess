<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;

final class RuleIsNotApplicable extends IllegalAction
{
    /**
     * Create an exception that rule is not applicable to the action.
     */
    public function __construct()
    {
        parent::__construct('Rule is not applicable to the action.');
    }
}
