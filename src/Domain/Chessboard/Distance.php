<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard;

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

    private function __construct(int $rankDistance = 0, int $fileDistance = 0)
    {
        $this->rankDistance = $rankDistance;
        $this->fileDistance = $fileDistance;
    }

    public static function calculate(Coordinates $from, Coordinates $to)
    {
        return new Distance(
            abs($to->rank() - $from->rank()),
            abs(ord($to->file()) - ord($from->file()))
        );
    }

    public function isHigherThan(int $distance)
    {
        return $this->rankDistance > $distance || $this->fileDistance > $distance;
    }
}
