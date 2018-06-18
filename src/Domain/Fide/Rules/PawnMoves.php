<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

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
                $this->movedPawns->attach($event->piece());
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(Move $move): bool
    {
        return $move->piece() instanceof Pawn && ($this->isLegalMove($move) || $this->isLegalCapture($move));
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

    /**
     * {@inheritdoc}
     */
    public function apply(Move $move, Game $game): void
    {
        if (
            $this->isLegalMove($move) && $game->isPositionOccupied($move->destination())
            || $this->isLegalCapture($move) && !$game->isPositionOccupiedByOpponentOf($move->destination(), $move->piece()->color())
        ) {
            throw new MoveToIllegalPosition($move);
        }

        $this->validateNotIntervenedMove($move, $game);
    }
}
