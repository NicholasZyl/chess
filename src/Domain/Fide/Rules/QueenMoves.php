<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;

final class QueenMoves implements Rule
{
    use NotIntervenedMove;

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
        return $action instanceof Move && $action->piece() instanceof Queen;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if (!$this->isApplicable($action)) {
            throw new RuleIsNotApplicable();
        }
        /** @var Move $action */

        if (!$action->inKnownDirection()) {
            throw new MoveToIllegalPosition($action);
        }

        $this->validateNotIntervenedMove($action, $board);
    }
}
