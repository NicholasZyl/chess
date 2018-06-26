<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;

final class KnightMoves implements Rule
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
    public function applyAfter(Event $event, Board $board, Rules $rules): array
    {
        // No specific rules to apply.
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(Action $action): bool
    {
        return $action instanceof Move && $action->piece() instanceof Knight && $this->isMoveToValidPosition($action);
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
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if (!$action instanceof Move) {
            throw new RuleIsNotApplicable();
        }

        if (!$this->isApplicable($action)) {
            throw new MoveToIllegalPosition($action);
        }
    }
}
