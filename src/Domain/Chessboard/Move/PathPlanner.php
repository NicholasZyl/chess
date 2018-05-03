<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class PathPlanner
{
    private $coordinates = [];

    public function step(CoordinatePair $pathSquare): PathPlanner
    {
        $this->coordinates[] = $pathSquare;

        return $this;
    }

    public function plan(): Path
    {
        return Path::forSquares($this->coordinates);
    }
}
