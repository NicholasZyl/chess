<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Rules\Exception;

use NicholasZyl\Chess\Domain\Piece\Rank;

final class MissingRule extends \RuntimeException
{
    /**
     * MissingRule constructor.
     *
     * @param Rank $rank
     */
    public function __construct(Rank $rank)
    {
        parent::__construct(sprintf('Rule for "%s" is missing.', $rank));
    }
}
