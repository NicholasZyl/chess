<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Board\Direction;

use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;

final class AlongRank implements Direction
{
    /**
     * @var bool
     */
    private $towardsKingside;

    public function __construct(bool $towardsKingside = true)
    {
        $this->towardsKingside = $towardsKingside;
    }

    /**
     * {@inheritdoc}
     */
    public function areOnSame(Coordinates $from, Coordinates $to): bool
    {
        return $from->rank() === $to->rank();
    }

    /**
     * {@inheritdoc}
     */
    public function nextCoordinatesTowards(Coordinates $from, Coordinates $to): Coordinates
    {
        if ($this->distanceBetween($from, $to) === 0) {
            throw new CoordinatesNotReachable($from, $to, $this);
        }
        $isTowardsKingside = ord($from->file()) < ord($to->file());

        return CoordinatePair::fromFileAndRank(
            chr(ord($from->file()) + ($isTowardsKingside ? 1 : -1)),
            $from->rank()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function distanceBetween(Coordinates $from, Coordinates $to): int
    {
        return abs(ord($to->file()) - ord($from->file()));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'along same rank';
    }

    /**
     * {@inheritdoc}
     */
    public function nextAlongFrom(Coordinates $position): Coordinates
    {
        return CoordinatePair::fromFileAndRank(
            chr(ord($position->file()) + ($this->towardsKingside ? 1 : -1)),
            $position->rank()
        );
    }
}
