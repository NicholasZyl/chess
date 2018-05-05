<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;

final class SquareIsOccupied extends \RuntimeException
{
    /**
     * SquareIsNotVacant constructor.
     *
     * @param Coordinates $coordinates
     */
    public function __construct(Coordinates $coordinates)
    {
        parent::__construct(sprintf('Square at %s is occupied.', $coordinates));
    }
}
