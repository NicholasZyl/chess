<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Square;

final class CoordinatePair
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
        $this->file = $file;
        $this->rank = $rank;
    }

    /**
     * Create coordinates from passed file and rank.
     *
     * @param string $file
     * @param int $rank
     *
     * @return CoordinatePair
     */
    public static function fromFileAndRank(string $file, int $rank): CoordinatePair
    {
        return new CoordinatePair($file, $rank);
    }

    /**
     * Create coordinates from string.
     *
     * @param string $coordinates
     *
     * @throws \InvalidArgumentException
     *
     * @return CoordinatePair
     */
    public static function fromString(string $coordinates): CoordinatePair
    {
        if (!preg_match('/^[a-z]\d$/i', $coordinates)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a proper format for coordinates.', $coordinates));
        }

        return new CoordinatePair(strtolower($coordinates[0]), intval($coordinates[1]));
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
     * Compare if is the same as other pair.
     *
     * @param CoordinatePair $other
     *
     * @return bool
     */
    public function equals(CoordinatePair $other): bool
    {
        return $this->file === $other->file && $this->rank === $other->rank;
    }

    public function isOnSameFile(CoordinatePair $other): bool
    {
        return $this->file === $other->file;
    }

    public function isOnSameRank(CoordinatePair $other): bool
    {
        return $this->rank === $other->rank;
    }

    public function isOnSameDiagonal(CoordinatePair $other): bool
    {
        return abs($this->rank - $other->rank) === abs(ord($this->file) - ord($other->file));
    }

    public function hasHigherRankThan(CoordinatePair $other): bool
    {
        return $this->rank > $other->rank;
    }

    public function towardsSquareAlongFile(CoordinatePair $other): CoordinatePair
    {
        return CoordinatePair::fromFileAndRank($this->file, $this->rank + ($this->rank < $other->rank ? 1 : -1));
    }

    public function towardsSquareAlongRank(CoordinatePair $other): CoordinatePair
    {
        return CoordinatePair::fromFileAndRank(chr((ord($this->file) + (ord($this->file) < ord($other->file) ? 1 : -1))), $this->rank);
    }

    public function towardsSquareAlongDiagonal(CoordinatePair $other): CoordinatePair
    {
        return CoordinatePair::fromFileAndRank(chr((ord($this->file) + (ord($this->file) < ord($other->file) ? 1 : -1))), $this->rank + ($this->rank < $other->rank ? 1 : -1));
    }
}
