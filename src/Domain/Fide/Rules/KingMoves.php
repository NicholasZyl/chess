<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;

final class KingMoves implements PieceMoves
{
    private const MOVE_TO_ADJOINING_SQUARE = 1;

    /**
     * @var \SplObjectStorage
     */
    private $movedKings;

    /**
     * Create King Moves rules.
     */
    public function __construct()
    {
        $this->movedKings = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function areApplicableFor(Piece $piece): bool
    {
        return $piece instanceof King;
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(Piece $piece, Move $move): void
    {
        if (!($move instanceof NotIntervened && $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE)) && (!$move instanceof Castling || $this->movedKings->contains($piece)) || $move->inDirection(new LShaped())) {
            throw new MoveNotAllowedForPiece($piece, $move);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyAfter(Event $event): void
    {
        if ($event instanceof Event\PieceWasMoved && $event->piece() instanceof King) {
            $this->movedKings->attach($event->piece());
        }
    }
}
