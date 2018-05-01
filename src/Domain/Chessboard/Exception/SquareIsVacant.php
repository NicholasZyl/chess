<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;

final class SquareIsVacant extends \RuntimeException
{
    /**
     * SquareIsVacant constructor.
     *
     * @param Coordinates $coordinates
     */
    public function __construct(Coordinates $coordinates)
    {
        parent::__construct(sprintf('Square at %s is currently vacant.', $coordinates));
    }
}