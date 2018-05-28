<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Piece;

final class PieceWasCaptured implements Event
{
    /**
     * @var Piece
     */
    private $piece;

    /**
     * @var Coordinates
     */
    private $at;

    /**
     * Create event that piece was captured at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $at
     */
    public function __construct(Piece $piece, Coordinates $at)
    {
        $this->piece = $piece;
        $this->at = $at;
    }

    /**
     * What piece was captured.
     *
     * @return Piece
     */
    public function piece(): Piece
    {
        return $this->piece;
    }

    /**
     * Where piece was captured.
     *
     * @return Coordinates
     */
    public function placedAt(): Coordinates
    {
        return $this->at;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Event $event): bool
    {
        return json_encode($event) === json_encode($this);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'event' => 'captured',
            'piece' => $this->piece,
            'placed_at' => $this->at,
        ];
    }
}
