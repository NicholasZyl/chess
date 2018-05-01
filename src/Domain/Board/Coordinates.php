<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board;

final class Coordinates
{
    /**
     * @var string
     */
    private $rank;

    /**
     * @var int
     */
    private $file;

    /**
     * Coordinates constructor.
     *
     * @param string $rank
     * @param int $file
     */
    private function __construct(string $rank, int $file)
    {
        $this->rank = strtolower($rank);
        $this->file = $file;
    }

    /**
     * Create coordinates from passed file and rank.
     *
     * @param string $rank
     * @param int $file
     *
     * @return Coordinates
     */
    public static function fromFileAndRank(string $rank, int $file)
    {
        return new Coordinates($rank, $file);
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
        return $this->rank . $this->file;
    }
}
