<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Rules\MoveRule;

final class PawnMoves implements MoveRule
{
    use NotIntervenedMove;

    private const MOVE_TO_ADJOINING_SQUARE = 1;
    private const MOVE_ADVANCING_TWO_SQUARES = 2;

    /**
     * {@inheritdoc}
     */
    public function priority(): int
    {
        return self::STANDARD_PRIORITY;
    }

    /**
     * @var \SplObjectStorage
     */
    private $movedPawns;

    /**
     * @var Coordinates|null
     */
    private $enPassantPossibileAt;

    /**
     * Create Pawn Moves rules.
     */
    public function __construct()
    {
        $this->movedPawns = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function applyAfter(Event $event, Game $game): array
    {
        if ($event instanceof Event\PieceWasMoved) {
            if ($event->piece() instanceof Pawn) {
                return $this->onPawnMoved($event, $game);
            } else {
                $this->enPassantPossibileAt = null;
            }
        }

        return [];
    }

    /**
     * Handle event that pawn was moved.
     *
     * @param Event\PieceWasMoved $event
     * @param Game $game
     *
     * @return Event[]
     */
    private function onPawnMoved(Event\PieceWasMoved $event, Game $game): array
    {
        $events = [];

        $this->movedPawns->attach($event->piece());
        if ($event->destination()->equals($this->enPassantPossibileAt)) {
            $position = $this->enPassantPossibileAt->nextTowards($event->source(), new AlongFile());
            $piece = $game->removePieceFromBoard($position);
            $this->enPassantPossibileAt = null;

            $events[] = new Event\PieceWasCaptured($piece, $position);
        } elseif ($event->wasOverDistanceOf(self::MOVE_ADVANCING_TWO_SQUARES)) {
            $this->enPassantPossibileAt = $event->destination()->nextTowards($event->source(), new AlongFile());
        } else {
            $this->enPassantPossibileAt = null;
        }

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(Move $move): bool
    {
        return $move->piece() instanceof Pawn;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Move $move, Game $game): void
    {
        if (!$this->isApplicable($move)) {
            throw new MoveToIllegalPosition($move);
        }
        $isLegalMove = $this->isLegalMove($move);
        $isLegalCapture = $this->isLegalCapture($move);

        if (!$isLegalMove && !$isLegalCapture) {
            throw new MoveToIllegalPosition($move);
        }

        if ($isLegalMove && $game->isPositionOccupied($move->destination())) {
            throw new MoveToIllegalPosition($move);
        }

        if ($isLegalCapture
            && !$game->isPositionOccupiedByOpponentOf($move->destination(), $move->piece()->color())
            && !$move->destination()->equals($this->enPassantPossibileAt)) {
            throw new MoveToIllegalPosition($move);
        }

        $this->validateNotIntervenedMove($move, $game);
    }

    /**
     * Is a legal move for given pawn.
     *
     * @param Move $move
     *
     * @return bool
     */
    private function isLegalMove(Move $move): bool
    {
        return $move->inDirection(new Forward($move->piece()->color(), new AlongFile()))
            && (
                !$this->movedPawns->contains($move->piece()) && $move->isOverDistanceOf(self::MOVE_ADVANCING_TWO_SQUARES)
                || $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE)
            );
    }

    /**
     * Is a legal capture for given pawn.
     *
     * @param Move $move
     *
     * @return bool
     */
    private function isLegalCapture(Move $move): bool
    {
        return $move->inDirection(new Forward($move->piece()->color(), new AlongDiagonal()))
            && $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE);
    }
}
