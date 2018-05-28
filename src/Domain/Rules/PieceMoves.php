<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Piece\Piece;
use NicholasZyl\Chess\Domain\Move;

interface PieceMoves
{
    /**
     * Are move rules applicable for given piece.
     *
     * @param Piece $piece
     *
     * @return bool
     */
    public function areApplicableFor(Piece $piece): bool;

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
    public function verify(Piece $piece, Move $move): void;

    /**
     * Apply rule after event had happened.
     *
     * @param Event $event
     *
     * @return void
     */
    public function applyAfter(Event $event): void;
}