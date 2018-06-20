<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\BoardException;

final class OutOfBoard extends BoardException
{
    /**
     * Create exception for coordinates that are out of board.
     *
     * @param Coordinates $coordinates
     */
    public function __construct(Coordinates $coordinates)
    {
        parent::__construct(sprintf('Position %s is out of board.', $coordinates));
    }
}
