<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;

final class IllegalMove extends InvalidMove
{
    /**
     * IllegalMove constructor.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     */
    public function __construct(Coordinates $from, Coordinates $to)
    {
        parent::__construct(sprintf('Move from %s to %s is illegal', $from, $to));
    }
}
