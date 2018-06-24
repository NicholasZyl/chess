<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Rules\MoveRule;

final class KnightMoves implements MoveRule
{
    private const DISTANCE_TO_THE_NEAREST_COORDINATES = 2;

    /**
     * {@inheritdoc}
     */
    public function priority(): int
    {
        return self::STANDARD_PRIORITY;
    }

    /**
     * {@inheritdoc}
     */
    public function applyAfter(Event $event, Game $game): array
    {
        // No specific rules to apply.
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(Move $move): bool
    {
        return $move->piece() instanceof Knight && $this->isMoveToValidPosition($move);
    }

    /**
     * Check if move is made to valid position meaning the nearest position not on same file, rank nor diagonal.
     *
     * @param Move $move
     * 
     * @return bool
     */
    private function isMoveToValidPosition(Move $move): bool
    {
        $alongFileDirection = new AlongFile();
        $alongRankDirection = new AlongRank();

        $alongFile = $alongFileDirection->areOnSame($move->source(), $move->destination());
        $alongRank = $alongRankDirection->areOnSame($move->source(), $move->destination());
        $alongDiagonal = (new AlongDiagonal())->areOnSame($move->source(), $move->destination());

        $distanceAlongFile = $move->source()->distanceTo($move->destination(), $alongFileDirection);
        $distanceAlongRank = $move->source()->distanceTo($move->destination(), $alongRankDirection);

        return !$alongFile && !$alongRank && !$alongDiagonal
            && $distanceAlongFile <= self::DISTANCE_TO_THE_NEAREST_COORDINATES && $distanceAlongRank <= self::DISTANCE_TO_THE_NEAREST_COORDINATES;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Move $move, Game $game): void
    {
        if (!$this->isApplicable($move)) {
            throw new MoveToIllegalPosition($move);
        }
    }
}
