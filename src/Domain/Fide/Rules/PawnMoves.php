<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Chessboard;
use NicholasZyl\Chess\Domain\Fide\Event\PawnReachedPromotion;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;

final class PawnMoves implements PieceMovesRule
{
    use NotIntervenedMove;

    private const MOVE_TO_ADJOINING_SQUARE = 1;
    private const MOVE_ADVANCING_TWO_SQUARES = 2;
    private const FURTHEST_RANKS = [
        Color::WHITE => Chessboard::HIGHEST_RANK,
        Color::BLACK => Chessboard::LOWEST_RANK,
    ];

    /**
     * @var \SplObjectStorage
     */
    private $movedPawns;

    /**
     * @var Coordinates|null
     */
    private $enPassantPossibileAt;

    /**
     * @var Coordinates|null
     */
    private $promotionPosition;

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
    public function applyAfter(Event $event, Board $board, Rules $rules): array
    {
        if ($event instanceof Event\PieceWasMoved) {
            if ($event->piece() instanceof Pawn) {
                return $this->onPawnMoved($event, $board);
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
     * @param Board $board
     *
     * @return Event[]
     */
    private function onPawnMoved(Event\PieceWasMoved $event, Board $board): array
    {
        $events = [];

        $this->movedPawns->attach($event->piece());
        if ($event->destination()->equals($this->enPassantPossibileAt)) {
            $position = $this->enPassantPossibileAt->nextTowards($event->source(), new AlongFile());
            $piece = $board->removePieceFrom($position);
            $this->enPassantPossibileAt = null;

            $events[] = new Event\PieceWasCaptured($piece, $position);
        } elseif ($event->wasOverDistanceOf(self::MOVE_ADVANCING_TWO_SQUARES)) {
            $this->enPassantPossibileAt = $event->destination()->nextTowards($event->source(), new AlongFile());
        } else {
            $this->enPassantPossibileAt = null;
        }
        if ($event->destination()->rank() === self::FURTHEST_RANKS[(string)$event->piece()->color()]) {
            $this->promotionPosition = $event->destination();
            $events[] = new PawnReachedPromotion($event->piece(), $this->promotionPosition);
        }

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableTo(Action $action): bool
    {
        return $action instanceof Move && $action->piece() instanceof Pawn
            || $action instanceof Exchange;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if ($action instanceof Move) {
            $this->applyToMove($action, $board);
        } elseif ($action instanceof Exchange) {
            $this->applyToExchange($action);
        } else {
            throw new RuleIsNotApplicable();
        }
    }

    /**
     * Apply rules to the move.
     *
     * @param Move $move
     * @param Board $board
     *
     * @return void
     */
    private function applyToMove(Move $move, Board $board): void
    {
        if (!$this->isApplicableTo($move)) {
            throw new MoveToIllegalPosition($move);
        }
        $isLegalMove = $this->isLegalMove($move);
        $isLegalCapture = $this->isLegalCapture($move);

        if (!$isLegalMove && !$isLegalCapture) {
            throw new MoveToIllegalPosition($move);
        }

        if ($isLegalMove && $board->isPositionOccupied($move->destination())) {
            throw new MoveToIllegalPosition($move);
        }

        if ($isLegalCapture
            && !$board->isPositionOccupiedBy($move->destination(), $move->piece()->color()->opponent())
            && !$move->destination()->equals($this->enPassantPossibileAt)) {
            throw new MoveToIllegalPosition($move);
        }

        $this->validateNotIntervenedMove($move, $board);
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
     * Apply rules to the exchange.
     *
     * @param Exchange $exchange
     *
     * @return void
     */
    private function applyToExchange(Exchange $exchange): void
    {
        if (!$exchange->position()->equals($this->promotionPosition) || $exchange->pieceToExchangeWith() instanceof Pawn || $exchange->pieceToExchangeWith() instanceof King) {
            throw new ExchangeIsNotAllowed($exchange->position());
        }
        $this->promotionPosition = null;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableFor(Piece $piece): bool
    {
        return $piece instanceof Pawn;
    }

    /**
     * {@inheritdoc}
     */
    public function getLegalDestinationsFrom(Piece $piece, Coordinates $actualPosition, Board $board): \Generator
    {
        $towardsHigherRank = Color::white()->is($piece->color());
        $forwardAlongFile = new AlongFile($towardsHigherRank);
        /** @var Board\Direction[] $captureDirections */
        $captureDirections = [
            new AlongDiagonal(true, $towardsHigherRank),
            new AlongDiagonal(false, $towardsHigherRank),
        ];

        $destinationToAdjoiningSquare = $forwardAlongFile->nextAlongFrom($actualPosition);
        if (!$board->isPositionOccupied($destinationToAdjoiningSquare)) {
            yield $destinationToAdjoiningSquare;
        }

        if ($actualPosition->rank() === ($towardsHigherRank ? 2 : 7) && !$this->movedPawns->contains($piece)) {
            $twoSquaresForward = $forwardAlongFile->nextAlongFrom($destinationToAdjoiningSquare);
            if (!$board->isPositionOccupied($twoSquaresForward)) {
                yield $twoSquaresForward;
            }
        }

        foreach ($captureDirections as $direction) {
            try {
                $captureDestination = $direction->nextAlongFrom($actualPosition);
                if ($board->isPositionOccupiedBy($captureDestination, $piece->color()->opponent()) || $captureDestination->equals($this->enPassantPossibileAt)) {
                    yield $captureDestination;
                }
            } catch (OutOfBoard $outOfBoard) {
                // Ignore
            }
        }
    }
}
