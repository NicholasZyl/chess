<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\BoardException;
use NicholasZyl\Chess\Domain\Piece\Color;

final class PositionOccupiedByAnotherColor extends BoardException
{
    /**
     * @var Coordinates
     */
    private $coordinates;

    /**
     * @var Color
     */
    private $color;

    /**
     * Create an exception that position is occupied by a color.
     *
     * @param Coordinates $coordinates
     * @param Color $color
     */
    public function __construct(Coordinates $coordinates, Color $color)
    {
        $this->coordinates = $coordinates;
        $this->color = $color;
        parent::__construct(sprintf('Position %s is occupied by %s.', $coordinates, $color));
    }

    /**
     * Get the occupied position.
     *
     * @return Coordinates
     */
    public function coordinates(): Coordinates
    {
        return $this->coordinates;
    }

    /**
     * Get the color occupying the position.
     *
     * @return Color
     */
    public function color(): Color
    {
        return $this->color;
    }
}
