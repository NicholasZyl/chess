<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Coordinates;

final class OutOfBoardPosition extends \RuntimeException
{
    public function __construct(Coordinates $coordinates)
    {
        parent::__construct(sprintf('Position %s is out of board.', $coordinates));
    }
}
