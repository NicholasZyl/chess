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
        if (!in_array($file, range('a', 'h'), true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a proper file.', $file));
        }
        if (!in_array($rank, range(1, 8), true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a proper rank.', $rank));
        }
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
    public static function fromFileAndRank(string $file, int $rank): Coordinates
    {
        return new Coordinates($file, $rank);
    }

    /**
     * Create coordinates from string.
     *
     * @param string $coordinates
     *
     * @throws \InvalidArgumentException
     *
     * @return Coordinates
     */
    public static function fromString(string $coordinates): Coordinates
    {
        if (!preg_match('/^[a-z]\d$/i', $coordinates)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a proper format for coordinates.', $coordinates));
        }

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
     * Get coordinates file part.
     *
     * @return string
     */
    public function file(): string
    {
        return $this->file;
    }

    /**
     * Get coordinates rank part.
     *
     * @return int
     */
    public function rank(): int
    {
        return $this->rank;
    }

    /**
     * Calculate distance to another coordinates.
     *
     * @param Coordinates $anotherCoordinates
     *
     * @return Distance
     */
    public function distance(Coordinates $anotherCoordinates): Distance
    {
        return Distance::calculate($this, $anotherCoordinates);
    }
}
