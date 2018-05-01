<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Piece;

final class Rank
{
    private const RANK_PAWN = 'pawn';
    private const RANK_KNIGHT = 'knight';
    private const RANK_BISHOP = 'bishop';
    private const RANK_ROOK = 'rook';
    private const RANK_QUEEN = 'queen';
    private const RANK_KING = 'king';
    private const AVAILABLE_RANKS = [
        self::RANK_PAWN,
        self::RANK_KNIGHT,
        self::RANK_BISHOP,
        self::RANK_ROOK,
        self::RANK_QUEEN,
        self::RANK_KING,
    ];

    /**
     * @var string
     */
    private $rankName;

    /**
     * Rank constructor.
     *
     * @param string $rankName
     *
     * @throws \InvalidArgumentException
     */
    private function __construct(string $rankName)
    {
        if (!in_array($rankName, self::AVAILABLE_RANKS, true)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid piece rank.', $rankName));
        }

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
        return new Rank(strtolower($rankName));
    }

    /**
     * Create rank for king.
     *
     * @return Rank
     */
    public static function king()
    {
        return new Rank(self::RANK_KING);
    }

    /**
     * Create rank for queen.
     *
     * @return Rank
     */
    public static function queen()
    {
        return new Rank(self::RANK_QUEEN);
    }

    /**
     * Create rank for rook.
     *
     * @return Rank
     */
    public static function rook()
    {
        return new Rank(self::RANK_ROOK);
    }

    /**
     * Create rank for bishop.
     *
     * @return Rank
     */
    public static function bishop()
    {
        return new Rank(self::RANK_BISHOP);
    }

    /**
     * Create rank for knight.
     *
     * @return Rank
     */
    public static function knight()
    {
        return new Rank(self::RANK_KNIGHT);
    }

    /**
     * Create rank for pawn.
     *
     * @return Rank
     */
    public static function pawn()
    {
        return new Rank(self::RANK_PAWN);
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
