<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard;

final class Coordinates
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var int
     */
    private $rank;

    /**
     * Coordinates constructor.
     *
     * @param string $file
     * @param int $rank
     */
    private function __construct(string $file, int $rank)
    {
        $this->file = strtolower($file);
        $this->rank = $rank;
    }

    /**
     * Create coordinates from passed file and rank.
     *
     * @param string $file
     * @param int $rank
     *
     * @return Coordinates
     */
    public static function fromFileAndRank(string $file, int $rank)
    {
        return new Coordinates($file, $rank);
    }

    /**
     * Create coordinates from string.
     *
     * @param string $coordinates
     *
     * @return Coordinates
     */
    public static function fromString(string $coordinates)
    {
        return new Coordinates($coordinates[0], intval($coordinates[1]));
    }

    /**
     * Represent coordinates as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->file . $this->rank;
    }

    /**
     * Calculate distance between ranks of two coordinates.
     *
     * @param Coordinates $anotherCoordinates
     *
     * @return int
     */
    public function rankDistance(Coordinates $anotherCoordinates): int
    {
        return abs($anotherCoordinates->rank - $this->rank);
    }

    /**
     * Calculate distance between files of two coordinates.
     *
     * @param Coordinates $anotherCoordinates
     *
     * @return int
     */
    public function fileDistance(Coordinates $anotherCoordinates): int
    {
        return abs(ord($anotherCoordinates->file) - ord($this->file));
    }
}
