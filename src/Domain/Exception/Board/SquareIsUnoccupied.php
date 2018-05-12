<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\BoardException;

final class SquareIsUnoccupied extends BoardException
{
    /**
     * Create exception for situation when square is unoccupied while it was expected to be.
     *
     * @param Coordinates $coordinates
     */
    public function __construct(Coordinates $coordinates)
    {
        parent::__construct(sprintf('Square at %s is unoccupied.', $coordinates));
    }
}