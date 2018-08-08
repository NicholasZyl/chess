<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Attack;
use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Action\Movement;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Chessboard;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Event\PawnReachedPromotion;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ActionNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\RuleIsNotApplicable;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;

final class PawnMoves implements PieceMovesRule
{
    use NotIntervenedMove;

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
    private const WHITE_STARTING_POSITION_RANK = 2;

    private const BLACK_STARTING_POSITION_RANK = 7;

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
        return $this->promotionPosition !== null
            || $action instanceof Movement && $action->piece() instanceof Pawn
            || $action instanceof Exchange;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Action $action, Board $board, Rules $rules): void
    {
        if (!$this->isApplicableTo($action)) {
            throw new RuleIsNotApplicable();
        }

        if ($this->promotionPosition !== null && !$action instanceof Exchange) {
            throw new ActionNotAllowed('pawn that reached promotion square must be exchanged');
        } elseif ($action instanceof Attack) {
            $this->applyToAttack($action, $board);
        } elseif ($action instanceof Move) {
            $this->applyToMove($action, $board);
        } elseif ($action instanceof Exchange) {
            $this->applyToExchange($action);
        } else {
            throw new RuleIsNotApplicable();
        }
    }

    /**
     * Apply rules to the attack.
     *
     * @param Attack $attack
     * @param Board $board
     *
     * @return void
     */
    private function applyToAttack(Attack $attack, Board $board): void
    {
        $this->applyToMove($attack, $board);
        if (!$attack->inDirection(new AlongDiagonal())) {
            throw new MoveToIllegalPosition($attack);
        }
    }

    /**
     * Apply rules to the move.
     *
     * @param Movement $move
     * @param Board $board
     *
     * @return void
     */
    private function applyToMove(Movement $move, Board $board): void
    {
        if (!in_array($move->destination(), iterator_to_array($this->getLegalDestinationsFrom($move->piece(), $move->source(), $board)))) {
            throw new MoveToIllegalPosition($move);
        }
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
        if (!$exchange->position()->equals($this->promotionPosition) || $this->promotionPosition->rank() !== self::FURTHEST_RANKS[(string)$exchange->pieceToExchangeWith()->color()] || $exchange->pieceToExchangeWith() instanceof Pawn || $exchange->pieceToExchangeWith() instanceof King) {
            throw new ExchangeIsNotAllowed($exchange->position());
        }
        $this->promotionPosition = null;
    }

    /**
     * {@inheritdoc}
     */
    public function isFor(): string
    {
        return Pawn::class;
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
        $isAdjoiningSquareOccupied = $board->isPositionOccupied($destinationToAdjoiningSquare);
        if (!$isAdjoiningSquareOccupied) {
            yield $destinationToAdjoiningSquare;
        }

        if ($actualPosition->rank() === ($towardsHigherRank ? self::WHITE_STARTING_POSITION_RANK : self::BLACK_STARTING_POSITION_RANK) && !$this->movedPawns->contains($piece)) {
            $twoSquaresForward = $forwardAlongFile->nextAlongFrom($destinationToAdjoiningSquare);
            if (!$isAdjoiningSquareOccupied && !$board->isPositionOccupied($twoSquaresForward)) {
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
