<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Rules\Exception;

final class IncompleteRules extends \RuntimeException
{
    public function __construct(array $missingRanksRules)
    {
        parent::__construct(sprintf('Rules are missing for: %s', implode(', ', $missingRanksRules)));
    }
}
