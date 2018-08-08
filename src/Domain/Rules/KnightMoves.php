<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Movement;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;

final class KnightMoves implements PieceMovesRule
{
    private const AVAILABLE_MOVES = [
        [1, 2,],
        [2, 1,],
        [2, -1,],
        [1, -2,],
        [-1, -2,],
        [-2, -1,],
        [-2, 1,],
        [-1, 2,],
    ];

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
    public function isApplicableTo(Action $action): bool
    {
        return $action instanceof Movement && $action->piece() instanceof Knight;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if (!$this->isApplicableTo($action)) {
            throw new RuleIsNotApplicable();
        }
        /** @var Movement $action */
        if (!in_array($action->destination(), iterator_to_array($this->getLegalDestinationsFrom($action->piece(), $action->source(), $board)))) {
            throw new MoveToIllegalPosition($action);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isFor(): string
    {
        return Knight::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLegalDestinationsFrom(Piece $piece, Coordinates $actualPosition, Board $board): \Generator
    {
        foreach (self::AVAILABLE_MOVES as $move) {
            try {
                $destination = CoordinatePair::fromFileAndRank(chr(ord($actualPosition->file()) + $move[0]), $actualPosition->rank() + $move[1]);
                if (!$board->isPositionOccupiedBy($destination, $piece->color())) {
                    yield $destination;
                }
            } catch (OutOfBoard $outOfBoard) {
                // skip
            }
        }
    }
}
