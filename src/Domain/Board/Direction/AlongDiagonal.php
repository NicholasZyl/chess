<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board\Direction;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;

final class AlongDiagonal implements Direction
{
    /**
     * @var bool
     */
    private $towardsKingside;

    /**
     * @var bool
     */
    private $towardsHigherRank;

    public function __construct(bool $towardsKingside = true, bool $towardsHigherRank = true)
    {
        $this->towardsKingside = $towardsKingside;
        $this->towardsHigherRank = $towardsHigherRank;
    }

    /**
     * {@inheritdoc}
     */
    public function areOnSame(Coordinates $from, Coordinates $to): bool
    {
        return abs($from->rank() - $to->rank()) === abs(ord($from->file()) - ord($to->file()));
    }

    /**
     * {@inheritdoc}
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        if (!$this->areOnSame($from, $to)) {
            throw new CoordinatesNotReachable($from, $to, $this);
        }

        $isTowardsKingside = ord($from->file()) < ord($to->file());
        $isTowardsHigherRank = $from->rank() < $to->rank();

        return CoordinatePair::fromFileAndRank(
            chr(ord($from->file()) + ($isTowardsKingside ? 1 : -1)),
            $from->rank() + ($isTowardsHigherRank ? 1 : -1)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function distanceBetween(Coordinates $from, Coordinates $to): int
    {
        if (!$this->areOnSame($from, $to)) {
            throw new CoordinatesNotReachable($from, $to, $this);
        }

        return abs($to->rank() - $from->rank());
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'along same diagonal';
    }

    /**
     * {@inheritdoc}
     */
    public function nextAlongFrom(Coordinates $position): Coordinates
    {
        return CoordinatePair::fromFileAndRank(
            chr(ord($position->file()) + ($this->towardsKingside ? 1 : -1)),
            $position->rank() + ($this->towardsHigherRank ? 1 : -1)
        );
    }
}
