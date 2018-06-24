<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Event;

use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Piece;

final class InCheck implements Event
{
    /**
     * @var Piece
     */
    private $piece;

    /**
     * Create an event that piece is in check.
     *
     * @param Piece $piece
     */
    public function __construct(Piece $piece)
    {
        $this->piece = $piece;
    }

    /**
     * Which piece is in check.
     *
     * @return Piece
     */
    public function piece(): Piece
    {
        return $this->piece;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(?Event $anotherEvent): bool
    {
        return $anotherEvent instanceof self && $this->piece->isSameAs($anotherEvent->piece());
    }
}
