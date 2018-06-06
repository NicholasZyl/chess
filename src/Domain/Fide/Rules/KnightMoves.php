<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;

final class KnightMoves implements PieceMoves
{
    /**
     * {@inheritdoc}
     */
    public function isApplicableFor(Piece $piece): bool
    {
        return $piece instanceof Knight;
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(Piece $piece, Move $move): void
    {
        if (!$move instanceof OverOtherPieces || !$move->inDirection(new LShaped())) {
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
