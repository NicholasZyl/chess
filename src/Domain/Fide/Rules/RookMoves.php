<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Piece\Piece;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;

final class RookMoves implements PieceMoves
{
    /**
     * @var \SplObjectStorage|Rook[]
     */
    private $movedRooks;

    /**
     * Create Rook Moves rules.
     */
    public function __construct()
    {
        $this->movedRooks = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function areApplicableFor(Piece $piece): bool
    {
        return $piece instanceof Rook;
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(Piece $piece, Move $move): void
    {
        if (!$move instanceof NotIntervened && (!$move instanceof Castling || $this->movedRooks->contains($piece)) || !($move->inDirection(new AlongFile()) || $move->inDirection(new AlongRank()))) {
            throw new MoveNotAllowedForPiece($piece, $move);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyAfter(Event $event): void
    {
        if ($event instanceof Event\PieceWasMoved && $event->piece() instanceof Rook) {
            $this->movedRooks->attach($event->piece());
        }
    }
}
