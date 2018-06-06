<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;

final class BishopMoves implements PieceMoves
{
    /**
     * {@inheritdoc}
     */
    public function isApplicableFor(Piece $piece): bool
    {
        return $piece instanceof Bishop;
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(Piece $piece, Move $move): void
    {
        if (!$move instanceof NotIntervened || !$move->inDirection(new AlongDiagonal())) {
            throw new MoveNotAllowedForPiece($piece, $move);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyAfter(Event $event): void
    {
        // No specific rules to apply.
    }
}
