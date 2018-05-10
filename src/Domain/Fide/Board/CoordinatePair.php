<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\UnknownDirection;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;

final class CoordinatePair implements Coordinates
{
    const LOWEST_FILE = 'a';
    const HIGHEST_FILE = 'h';
    const LOWEST_RANK = 1;
    const HIGHEST_RANK = 8;

    /**
     * @var string
     */
    private $file;

    /**
     * @var int
     */
    private $rank;

    /**
     * Create coordinate pair from passed file and rank.
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
     * Create coordinate pair from string.
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
     * Create a new coordinate pair from valid file and rank.
     *
     * @param string $file
     * @param int $rank
     */
    private function __construct(string $file, int $rank)
    {
        if (!in_array($file, self::validFiles(), true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a proper file.', $file));
        }
        if (!in_array($rank, self::validRanks(), true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a proper rank.', $rank));
        }
        $this->file = $file;
        $this->rank = $rank;
    }

    /**
     * Get all valid files.
     *
     * @return array
     */
    public static function validFiles(): array
    {
        return range(self::LOWEST_FILE, self::HIGHEST_FILE);
    }

    /**
     * Get all valid ranks.
     *
     * @return array
     */
    public static function validRanks(): array
    {
        return range(self::LOWEST_RANK, self::HIGHEST_RANK);
    }

    /**
     * {@inheritdoc}
     */
    public function file(): string
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function rank(): int
    {
        return $this->rank;
    }

    /**
     * {@inheritdoc}
     */
    public function nextTowards(Coordinates $destination, Direction $direction): Coordinates
    {
        return $direction->nextCoordinatesTowards($this, $destination);
    }

    /**
     * {@inheritdoc}
     */
    public function directionTo(Coordinates $coordinates): Direction
    {
        if ($this->isOnSameDiagonal($coordinates)) {
            return new AlongDiagonal();
        }
        if ($this->isOnSameFile($coordinates)) {
            return new AlongFile();
        }
        if ($this->isOnSameRank($coordinates)) {
            return new AlongRank();
        }

        throw new UnknownDirection($this, $coordinates);
    }

    /**
     * Is pair on the same file as another.
     *
     * @param Coordinates $other
     *
     * @return bool
     */
    private function isOnSameFile(Coordinates $other): bool
    {
        return $this->file() === $other->file();
    }

    /**
     * Is pair on the same rank as another.
     *
     * @param Coordinates $other
     *
     * @return bool
     */
    private function isOnSameRank(Coordinates $other): bool
    {
        return $this->rank() === $other->rank();
    }

    /**
     * Is pair on the same diagonal as another.
     *
     * @param Coordinates $other
     *
     * @return bool
     */
    private function isOnSameDiagonal(Coordinates $other): bool
    {
        return abs($this->rank() - $other->rank()) === abs(ord($this->file()) - ord($other->file()));
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Coordinates $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->file === $other->file && $this->rank === $other->rank;
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
}
