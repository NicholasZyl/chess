<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules\Exception;

final class IncompleteRules extends \RuntimeException
{
    /**
     * IncompleteRules constructor.
     *
     * @param array $missingRanksRules
     */
    public function __construct(array $missingRanksRules)
    {
        parent::__construct(sprintf('Rules are missing for: %s', implode(', ', $missingRanksRules)));
    }
}
