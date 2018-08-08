<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;

final class KingCheck implements Rule
{
    /**
     * @var Coordinates[]
     */
    private $kingsPositions;

    /**
     * Create rules for checks.
     *
     * @param Coordinates $whiteKingPosition
     * @param Coordinates $blackKingPosition
     */
    public function __construct(Coordinates $whiteKingPosition, Coordinates $blackKingPosition)
    {
        $this->kingsPositions = [
            Color::WHITE => $whiteKingPosition,
            Color::BLACK => $blackKingPosition,
        ];
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

            $isOpponentChecked = $board->isPositionAttackedBy($this->kingsPositions[(string)$color->opponent()], $color, $rules);
            if ($isOpponentChecked) {
                $events[] = new Event\InCheck($color->opponent());
            }
            if (!$board->hasLegalMove($color->opponent(), $rules)) {
                $events[] = $isOpponentChecked ? new Event\Checkmated($color->opponent()) : new Event\Stalemate();
            };
        }

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableTo(Action $action): bool
    {
        return $action instanceof Move && !$action instanceof Action\Attack;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if (!$this->isApplicableTo($action)) {
            throw new IllegalAction\RuleIsNotApplicable();
        }

        /** @var Move $action */
        $color = $action->piece()->color();
        $kingPosition = $this->kingsPositions[(string)$color];
        $moveDestination = $action->destination();
        if ($action->piece() instanceof King) {
            $kingPosition = $moveDestination;
        }
        if ($board->isPositionOccupiedBy($moveDestination, $color->opponent())) {
            $pieceToCapture = $board->pickPieceFrom($moveDestination);
        }

        try {
            if ($board->isPositionAttackedBy($kingPosition, $color->opponent(), $rules)) {
                throw new IllegalAction\MoveExposesToCheck();
            }
        } finally {
            if (isset($pieceToCapture)) {
                $board->placePieceAt($pieceToCapture, $moveDestination);
            }
        }
    }
}
