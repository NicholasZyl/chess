<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;

final class OutOfBoardCoordinates extends \RuntimeException
{
    public function __construct(Coordinates $coordinates)
    {
        parent::__construct(sprintf('Coordinates %s are out of board.', $coordinates));
    }
}
