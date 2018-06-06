<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece;

interface PieceMoves
{
    /**
     * Are move rules applicable for given piece.
     *
     * @param Piece $piece
     *
     * @return bool
     */
    public function isApplicableFor(Piece $piece): bool;

    /**
     * Verify that piece may do given move.
     *
     * @param Piece $piece
     * @param Move $move
     *
     * @throws MoveNotAllowedForPiece
     *
     * @return void
     */
    public function mayMove(Piece $piece, Move $move): void;

    /**
     * Apply rule after event had happened.
     *
     * @param Event $event
     *
     * @return void
     */
    public function applyAfter(Event $event): void;
}