<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\BoardException;

final class SquareIsOccupied extends BoardException
{
    /**
     * @var Coordinates
     */
    private $coordinates;

    /**
     * Create exception for situation when square is occupied but it was expected to be.
     *
     * @param Coordinates $coordinates
     */
    public function __construct(Coordinates $coordinates)
    {
        parent::__construct(sprintf('Square at %s is occupied.', $coordinates));

        $this->coordinates = $coordinates;
    }

    /**
     * Get coordinates of the square.
     *
     * @return Coordinates
     */
    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }
}
