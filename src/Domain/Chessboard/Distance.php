<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Piece\Color;

final class Distance
{
    /**
     * @var int
     */
    private $rankDistance;

    /**
     * @var int
     */
    private $fileDistance;

    /**
     * Distance constructor.
     *
     * @param int $rankDistance
     * @param int $fileDistance
     */
    private function __construct(int $rankDistance = 0, int $fileDistance = 0)
    {
        $this->rankDistance = $rankDistance;
        $this->fileDistance = $fileDistance;
    }

    /**
     * Calculates distance between two coordinates.
     *
     * @param Coordinates $from
     * @param Coordinates $to
     *
     * @return Distance
     */
    public static function between(Coordinates $from, Coordinates $to): Distance
    {
        return new Distance(
            $to->rank() - $from->rank(),
            ord($to->file()) - ord($from->file())
        );
    }

    /**
     * Checks if distance between coordinates is higher than passed value in any direction.
     *
     * @param int $distance
     *
     * @return bool
     */
    public function isHigherThan(int $distance)
    {
        return abs($this->rankDistance) > $distance || abs($this->fileDistance) > $distance;
    }

    /**
     * Checks if distance is only vertical.
     *
     * @return bool
     */
    public function isVertical(): bool
    {
        return $this->fileDistance === 0;
    }

    /**
     * Checks if distance is only horizontal.
     *
     * @return bool
     */
    public function isHorizontal(): bool
    {
        return $this->rankDistance === 0;
    }

    /**
     * Checks if distance is only diagonal.
     *
     * @return bool
     */
    public function isDiagonal(): bool
    {
        return abs($this->fileDistance) === abs($this->rankDistance);
    }

    public function isForward(Color $color): bool
    {
        return $color->is(Color::white()) ? $this->rankDistance > 0 : $this->rankDistance < 0;
    }
}
