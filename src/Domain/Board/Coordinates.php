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

    private function __construct(string $rank, int $file)
    {
        $this->rank = $rank;
        $this->file = $file;
    }

    public static function fromString(string $coordinates)
    {
        return new Coordinates($coordinates[0], intval($coordinates[1]));
    }

    public function __toString(): string
    {
        return strtoupper($this->rank) . $this->file;
    }
}
