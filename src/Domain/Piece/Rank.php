<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Piece;

final class Rank
{
    /**
     * @var string
     */
    private $rankName;

    private function __construct(string $rankName)
    {
        $this->rankName = $rankName;
    }

    public static function fromString(string $rankName): Rank
    {
        return new Rank($rankName);
    }

    public function isSameAs(Rank $anotherRank): bool
    {
        return $this->rankName === $anotherRank->rankName;
    }
}
