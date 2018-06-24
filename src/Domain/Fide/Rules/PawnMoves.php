<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Rule;

final class PawnMoves implements Rule
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
    public function isApplicable(Action $action): bool
    {
        return $action instanceof Move && $action->piece() instanceof Pawn;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Game $game): void
    {
        if (!$action instanceof Move) {
            throw new RuleIsNotApplicable();
        }

        if (!$this->isApplicable($action)) {
            throw new MoveToIllegalPosition($action);
        }
        $isLegalMove = $this->isLegalMove($action);
        $isLegalCapture = $this->isLegalCapture($action);

        if (!$isLegalMove && !$isLegalCapture) {
            throw new MoveToIllegalPosition($action);
        }

        if ($isLegalMove && $game->isPositionOccupied($action->destination())) {
            throw new MoveToIllegalPosition($action);
        }

        if ($isLegalCapture
            && !$game->isPositionOccupiedByOpponentOf($action->destination(), $action->piece()->color())
            && !$action->destination()->equals($this->enPassantPossibileAt)) {
            throw new MoveToIllegalPosition($action);
        }

        $this->validateNotIntervenedMove($action, $game);
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
