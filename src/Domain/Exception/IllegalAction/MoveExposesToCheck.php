<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;

final class MoveExposesToCheck extends IllegalAction
{
    /**
     * Create an exception that move would expose the color to check.
     */
    public function __construct()
    {
        parent::__construct('Move would expose color to check.');
    }
}
