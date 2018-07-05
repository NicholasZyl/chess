<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;

final class RookMoves implements PieceMovesRule
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
    public function isApplicableTo(Action $action): bool
    {
        return $action instanceof Move && $this->isApplicableFor($action->piece());
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if (!$this->isApplicableTo($action)) {
            throw new RuleIsNotApplicable();
        }
        /** @var Move $action */

        if (!($action->inDirection(new AlongRank()) || $action->inDirection(new AlongFile()))) {
            throw new MoveToIllegalPosition($action);
        }

        $this->validateNotIntervenedMove($action, $board);
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableFor(Piece $piece): bool
    {
        return $piece instanceof Rook;
    }

    /**
     * {@inheritdoc}
     */
    public function getLegalDestinationsFrom(Piece $piece, Coordinates $actualPosition, Board $board): \Generator
    {
        /** @var Board\Direction[] $directions */
        $directions = [
            new AlongFile(true),
            new AlongRank(true),
            new AlongFile(false),
            new AlongRank(false),
        ];


        return $this->getNotIntervenedDestinationsForDirections($directions, $actualPosition, $board, $piece);
    }
}
