<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class MoveIsInvalid extends IllegalMove
{
    /**
     * Create exception for generally invalid move.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     */
    public function __construct(CoordinatePair $from, CoordinatePair $to)
    {
        parent::__construct(sprintf('Move between %s and %s is not possible.', $from, $to));
    }
}
