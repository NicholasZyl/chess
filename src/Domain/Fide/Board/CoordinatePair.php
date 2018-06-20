<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\UnknownDirection;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;

final class CoordinatePair implements Coordinates
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
     *
     * @throws \InvalidArgumentException
     */
    private function __construct(string $file, int $rank)
    {
        if (!in_array($file, range('a', 'z'), true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a proper file.', $file));
        }
        if ($rank < 0) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a proper rank.', $rank));
        }
        $this->file = $file;
        $this->rank = $rank;
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
        $alongDiagonal = new AlongDiagonal();
        if ($alongDiagonal->areOnSame($this, $coordinates)) {
            return $alongDiagonal;
        }
        $alongFile = new AlongFile();
        if ($alongFile->areOnSame($this, $coordinates)) {
            return $alongFile;
        }
        $alongRank = new AlongRank();
        if ($alongRank->areOnSame($this, $coordinates)) {
            return $alongRank;
        }

        throw new UnknownDirection($this, $coordinates);
    }

    /**
     * {@inheritdoc}
     */
    public function distanceTo(Coordinates $coordinates, Direction $direction): int
    {
        return $direction->distanceBetween($this, $coordinates);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(?Coordinates $other): bool
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
