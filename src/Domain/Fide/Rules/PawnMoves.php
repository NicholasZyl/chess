<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Piece;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;

final class PawnMoves implements PieceMoves
{
    private const MOVE_TO_ADJOINING_SQUARE = 1;
    private const MOVE_ADVANCING_TWO_SQUARES = 2;

    /**
     * @var \SplObjectStorage
     */
    private $movedPawns;

    /**
     * @var \SplObjectStorage[]
     */
    private $occupiedPositions;

    /**
     * Create Pawn Moves rules.
     */
    public function __construct()
    {
        $this->movedPawns = new \SplObjectStorage();

        $stringIndexedStorage = function() {
            return new class extends \SplObjectStorage {
                public function getHash($object)
                {
                    if ($object instanceof Coordinates || $object instanceof Color) {
                        return (string) $object;
                    }

                    return parent::getHash($object);
                }

            };
        };

        $this->occupiedPositions = $stringIndexedStorage();

        $this->occupiedPositions[Color::white()] = $stringIndexedStorage();
        $this->occupiedPositions[Color::black()] = $stringIndexedStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function areApplicableFor(Piece $piece): bool
    {
        return $piece instanceof Pawn;
    }

    /**
     * {@inheritdoc}
     */
    public function mayMove(Piece $piece, Move $move): void
    {
        if (!$this->isLegalMove($piece, $move) && !$this->isLegalCapture($piece, $move)) {
            throw new MoveNotAllowedForPiece($piece, $move);
        }
    }

    /**
     * Is a legal move for given pawn.
     *
     * @param Piece $piece
     * @param Move $move
     *
     * @return bool
     */
    private function isLegalMove(Piece $piece, Move $move): bool
    {
        return $move->inDirection(new Forward($piece->color(), new AlongFile()))
            && (
                !$this->movedPawns->contains($piece) && $move->isOverDistanceOf(self::MOVE_ADVANCING_TWO_SQUARES)
                || $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE)
            )
            && !$this->occupiedPositions[$piece->color()->opponent()]->contains($move->destination());
    }

    /**
     * Is a legal capture for given pawn.
     *
     * @param Piece $piece
     * @param Move $move
     *
     * @return bool
     */
    private function isLegalCapture(Piece $piece, Move $move): bool
    {
        return $move->inDirection(new Forward($piece->color(), new AlongDiagonal()))
            && $move->isOverDistanceOf(self::MOVE_TO_ADJOINING_SQUARE)
            && $this->occupiedPositions[$piece->color()->opponent()]->contains($move->destination());
    }

    /**
     * {@inheritdoc}
     */
    public function applyAfter(Event $event): void
    {
        if ($event instanceof Event\PieceWasMoved) {
            if ($event->piece() instanceof Pawn) {
                $this->movedPawns->attach($event->piece());
            }
            $this->occupiedPositions[$event->piece()->color()]->detach($event->source());
            $this->occupiedPositions[$event->piece()->color()]->attach($event->destination());
        }
    }
}
