<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Event;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Piece;

final class PawnReachedPromotion implements Event
{
    /**
     * @var Piece
     */
    private $pawn;
    /**
     * @var Coordinates
     */
    private $promotionSquare;

    /**
     * Create an event that pawn reached the square of promotion.
     *
     * @param Piece $pawn
     * @param Coordinates $promotionSquare
     */
    public function __construct(Piece $pawn, Coordinates $promotionSquare)
    {
        $this->pawn = $pawn;
        $this->promotionSquare = $promotionSquare;
    }

    /**
     * Get the pawn that reached the square of promotion.
     *
     * @return Piece
     */
    public function piece(): Piece
    {
        return $this->pawn;
    }

    /**
     * Get the coordinates of the square.
     *
     * @return Coordinates
     */
    public function square(): Coordinates
    {
        return $this->promotionSquare;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(?Event $anotherEvent): bool
    {
        return $anotherEvent instanceof self
            && $this->pawn->isSameAs($anotherEvent->pawn)
            && $this->promotionSquare->equals($anotherEvent->promotionSquare);
    }
}
