<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Rules\MoveRule;

final class KingMoves implements MoveRule
{
    private const MOVE_TO_ADJOINING_SQUARE = 1;

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
        return $move->piece() instanceof King && $move->inKnownDirection() && $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE);
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
