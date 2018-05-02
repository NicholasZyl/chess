<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;

final class SquareIsUnoccupied extends NotPermittedMove
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