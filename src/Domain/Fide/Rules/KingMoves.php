<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Piece;
use NicholasZyl\Chess\Domain\Move;

final class KingMoves
{
    private const MOVE_TO_ADJOINING_SQUARE = 1;

    /**
     * @var King[]
     */
    private $movedKings = [];

    /**
     * Are move rules applicable for given piece.
     *
     * @param Piece $piece
     *
     * @return bool
     */
    public function areApplicableFor(Piece $piece): bool
    {
        return $piece instanceof King;
    }

    /**
     * Verify that move for given piece is legal.
     *
     * @param Piece $piece
     * @param Move $move
     *
     * @throws MoveNotAllowedForPiece
     *
     * @return void
     */
    public function verify(Piece $piece, Move $move): void
    {
        if (!($move instanceof NotIntervened && $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE)) && (!$move instanceof Castling || in_array($piece, $this->movedKings)) || $move->inDirection(new LShaped())) {
            throw new MoveNotAllowedForPiece($piece, $move);
        }
    }

    /**
     * Apply rule after event had happened.
     *
     * @param Event $event
     *
     * @return void
     */
    public function applyAfter(Event $event): void
    {
        if ($event instanceof Event\PieceWasPlacedAt && $event->piece() instanceof King) {
            $this->movedKings []= $event->piece();
        }
    }
}
