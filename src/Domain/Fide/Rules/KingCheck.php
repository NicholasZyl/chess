<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;

final class KingCheck implements Rule
{
    private const WHITE_KING_INITIAL_POSITION = 'e1';
    private const BLACK_KING_INITIAL_POSITION = 'e8';

    /**
     * @var Coordinates[]
     */
    private $kingsPositions;

    /**
     * Create rules for checks.
     */
    public function __construct()
    {
        $this->kingsPositions = [
            Color::WHITE => CoordinatePair::fromString(self::WHITE_KING_INITIAL_POSITION),
            Color::BLACK => CoordinatePair::fromString(self::BLACK_KING_INITIAL_POSITION),
        ];
    }

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
        $events = [];

        if ($event instanceof Event\PieceWasMoved) {
            $color = $event->piece()->color();
            if ($event->piece() instanceof King) {
                $this->kingsPositions[(string)$color] = $event->destination();
            }

            if ($board->isPositionAttackedBy($this->kingsPositions[(string)$color], $color->opponent(), $rules)) {
                throw new IllegalAction\MoveExposesToCheck();
            }

            if ($board->isPositionAttackedBy($this->kingsPositions[(string)$color->opponent()], $color, $rules)) {
                $events[] = new Event\InCheck($color->opponent());
            }
        }

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(Action $action): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        throw new IllegalAction\RuleIsNotApplicable();
    }
}
