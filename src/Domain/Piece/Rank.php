<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Piece;

final class Rank
{
    /**
     * @var string
     */
    private $rankName;

    /**
     * Rank constructor.
     *
     * @param string $rankName
     */
    private function __construct(string $rankName)
    {
        $this->rankName = $rankName;
    }

    /**
     * Create rank from a string.
     *
     * @param string $rankName
     *
     * @return Rank
     */
    public static function fromString(string $rankName): Rank
    {
        return new Rank($rankName);
    }

    /**
     * Check if rank is same as another.
     *
     * @param Rank $anotherRank
     *
     * @return bool
     */
    public function isSameAs(Rank $anotherRank): bool
    {
        return $this->rankName === $anotherRank->rankName;
    }

    /**
     * Represent rank as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->rankName;
    }
}
