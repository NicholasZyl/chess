<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\CastlingPrevented;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Rules\MoveRule;

final class CastlingMove implements MoveRule
{
    private const CASTLING_MOVE_DISTANCE = 2;
    private const INITIAL_ROOK_POSITIONS = [
        'a1',
        'h1',
        'a8',
        'h8',
    ];

    /**
     * {@inheritdoc}
     */
    public function priority(): int
    {
        return self::HIGH_PRIORITY;
    }

    /**
     * @var \SplObjectStorage
     */
    private $movedKings;

    /**
     * @var string[]
     */
    private $rookPositionsAvailableForCastling;

    /**
     * @var bool
     */
    private $inCastling = false;

    /**
     * Create Castling Move rules.
     */
    public function __construct()
    {
        $this->movedKings = new \SplObjectStorage();
        $this->rookPositionsAvailableForCastling = array_flip(self::INITIAL_ROOK_POSITIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function applyAfter(Event $event, Game $game): array
    {
        if ($event instanceof Event\PieceWasMoved) {
            if ($event->piece() instanceof King) {
                $this->movedKings->attach($event->piece());
                if ($event->wasOverDistanceOf(self::CASTLING_MOVE_DISTANCE)) {
                    $rookPosition = CoordinatePair::fromFileAndRank($event->source()->file() < $event->destination()->file() ? 'h' : 'a', $event->source()->rank());
                    $rookDestination = $event->destination()->nextTowards($event->source(), new AlongRank());

                    $this->inCastling = true;

                    return $game->playMove($rookPosition, $rookDestination);
                }
            } elseif ($event->piece() instanceof Rook) {
                unset($this->rookPositionsAvailableForCastling[(string)$event->source()]);
                $this->inCastling = false;
            }
        } elseif ($event instanceof Event\PieceWasCaptured) {
            if ($event->piece() instanceof Rook) {
                unset($this->rookPositionsAvailableForCastling[(string)$event->capturedAt()]);
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(Move $move): bool
    {
        return ($move->piece() instanceof King
            && $move->inDirection(new AlongRank()) && $move->isOverDistanceOf(self::CASTLING_MOVE_DISTANCE))
            || ($move->piece() instanceof Rook && $this->inCastling);
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Move $move, Game $game): void
    {
        if (!$this->isApplicable($move)) {
            throw new MoveToIllegalPosition($move);
        }

        if ($move->piece() instanceof King) {
            $this->applyForKingsMove($move, $game);
        }
    }

    /**
     * Apply rule for King's part of teh castling.
     *
     * @param Move $move
     * @param Game $game
     *
     * @return void
     */
    private function applyForKingsMove(Move $move, Game $game): void
    {
        if ($this->movedKings->contains($move->piece())) {
            throw new CastlingPrevented($move);
        }

        $rookPosition = CoordinatePair::fromFileAndRank($move->source()->file() < $move->destination()->file() ? 'h' : 'a', $move->source()->rank());
        if (!array_key_exists((string)$rookPosition, $this->rookPositionsAvailableForCastling)) {
            throw new CastlingPrevented($move);
        }

        $alongRank = new AlongRank();
        $step = $move->source()->nextTowards($rookPosition, $alongRank);
        while (!$step->equals($rookPosition)) {
            if ($game->isPositionOccupied($step)) {
                throw new CastlingPrevented($move);
            }
            $step = $step->nextTowards($rookPosition, $alongRank);
        }

        $step = $move->source();
        $destination = $move->destination();
        while (!$step->equals($destination)) {
            if ($game->isPositionAttackedByOpponentOf($step, $move->piece()->color())) {
                throw new CastlingPrevented($move);
            }
            $step = $step->nextTowards($destination, $alongRank);
        }
        if ($game->isPositionAttackedByOpponentOf($step, $move->piece()->color())) {
            throw new CastlingPrevented($move);
        }
    }
}
