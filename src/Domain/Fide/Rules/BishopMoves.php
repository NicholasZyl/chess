<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Rule;

final class BishopMoves implements Rule
{
    use NotIntervenedMove;

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
    public function isApplicable(Action $action): bool
    {
        return $action instanceof Move && $action->piece() instanceof Bishop && $action->inDirection(new AlongDiagonal());
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Game $game): void
    {
        if (!$action instanceof Move) {
            throw new RuleIsNotApplicable();
        }

        if (!$this->isApplicable($action)) {
            throw new MoveToIllegalPosition($action);
        }

        $this->validateNotIntervenedMove($action, $game);
    }
}
