<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Piece;

final class PieceWasExchanged implements Event
{
    /**
     * @var Piece
     */
    private $piece;

    /**
     * @var Coordinates
     */
    private $position;

    /**
     * @var Piece
     */
    private $exchangedWithPiece;

    /**
     * Create an event that piece was exchanged to another one.
     *
     * @param Piece $piece
     * @param Piece $exchangedWithPiece
     * @param Coordinates $position
     */
    public function __construct(Piece $piece, Piece $exchangedWithPiece, Coordinates $position)
    {
        $this->piece = $piece;
        $this->position = $position;
        $this->exchangedWithPiece = $exchangedWithPiece;
    }

    /**
     * What piece was exchanged.
     *
     * @return Piece
     */
    public function piece(): Piece
    {
        return $this->piece;
    }

    /**
     * Piece with which exchange happened;
     *
     * @return Piece
     */
    public function exchangedWith(): Piece
    {
        return $this->exchangedWithPiece;
    }

    /**
     * Where the exchange occurred.
     *
     * @return Coordinates
     */
    public function position(): Coordinates
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(?Event $anotherEvent): bool
    {
        return $anotherEvent instanceof self
            && $anotherEvent->piece()->isSameAs($this->piece)
            && $anotherEvent->exchangedWith()->isSameAs($this->exchangedWithPiece)
            && $anotherEvent->position()->equals($this->position);
    }
}
