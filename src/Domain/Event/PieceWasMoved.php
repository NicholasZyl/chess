<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Piece;

final class PieceWasMoved implements Event
{
    /**
     * @var Piece
     */
    private $piece;

    /**
     * @var Coordinates
     */
    private $source;

    /**
     * @var Coordinates
     */
    private $destination;

    /**
     * Create event that piece moved from one coordinates to another.
     *
     * @param Piece $piece
     * @param Coordinates $source
     * @param Coordinates $destination
     */
    public function __construct(Piece $piece, Coordinates $source, Coordinates $destination)
    {
        $this->piece = $piece;
        $this->source = $source;
        $this->destination = $destination;
    }

    /**
     * What piece moved.
     *
     * @return Piece
     */
    public function piece(): Piece
    {
        return $this->piece;
    }

    /**
     * From where piece moved.
     *
     * @return Coordinates
     */
    public function source(): Coordinates
    {
        return $this->source;
    }

    /**
     * Where piece moved to.
     *
     * @return Coordinates
     */
    public function destination(): Coordinates
    {
        return $this->destination;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'event' => 'moved',
            'piece' => $this->piece,
            'source' => $this->source,
            'destination' => $this->destination,
        ];
    }
}
