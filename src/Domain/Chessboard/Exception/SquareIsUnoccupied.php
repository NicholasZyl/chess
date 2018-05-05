<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class SquareIsUnoccupied extends \RuntimeException
{
    /**
     * SquareIsVacant constructor.
     *
     * @param CoordinatePair $coordinates
     */
    public function __construct(CoordinatePair $coordinates)
    {
        parent::__construct(sprintf('Square at %s is unoccupied.', $coordinates));
    }
}