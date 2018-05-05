<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Position\Coordinates;

final class SquareIsUnoccupied extends \RuntimeException
{
    /**
     * SquareIsVacant constructor.
     *
     * @param Coordinates $coordinates
     */
    public function __construct(Coordinates $coordinates)
    {
        parent::__construct(sprintf('Square at %s is unoccupied.', $coordinates));
    }
}