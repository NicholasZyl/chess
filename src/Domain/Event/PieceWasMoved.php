<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Piece;

final class PieceWasMoved implements Event
{
    /**
     * @var Move
     */
    private $move;

    /**
     * Create an event that piece was moved from one position to another.
     *
     * @param Move $move
     */
    public function __construct(Move $move)
    {
        $this->move = $move;
    }

    /**
     * What piece moved.
     *
     * @return Piece
     */
    public function piece(): Piece
    {
        return $this->move->piece();
    }

    /**
     * From where piece moved.
     *
     * @return Coordinates
     */
    public function source(): Coordinates
    {
        return $this->move->source();
    }

    /**
     * Where piece moved to.
     *
     * @return Coordinates
     */
    public function destination(): Coordinates
    {
        return $this->move->destination();
    }

    /**
     * Check if move was made over expected distance.
     *
     * @param int $expectedDistance
     *
     * @return bool
     */
    public function wasOverDistanceOf(int $expectedDistance): bool
    {
        return $this->move->isOverDistanceOf($expectedDistance);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(?Event $anotherEvent): bool
    {
        return $anotherEvent instanceof self
            && $anotherEvent->piece()->isSameAs($this->piece())
            && $anotherEvent->source()->equals($this->source())
            && $anotherEvent->destination()->equals($this->destination());
    }
}
