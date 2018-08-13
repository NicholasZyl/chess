<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;

final class Turns implements Rule
{
    /**
     * @var Color
     */
    private $player;

    /**
     * Create rule for players to move alternately.
     * @param Color|null $startingColor
     */
    public function __construct(Color $startingColor = null)
    {
        $this->player = $startingColor ?? Color::white();
    }

    /**
     * {@inheritdoc}
     */
    public function applyAfter(Event $event, Board $board, Rules $rules): array
    {
        if ($event instanceof Event\PieceWasMoved) {
            $this->player = $this->player->opponent();
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableTo(Action $action): bool
    {
        return $action instanceof Action\Move;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if (!$this->player->is($action->player())) {
            throw new IllegalAction\ActionNotAllowed('players have to move alternately');
        }
    }
}
