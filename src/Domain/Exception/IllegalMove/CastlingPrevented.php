<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalMove;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Move;

final class CastlingPrevented extends IllegalMove
{
    /**
     * Create an exception for catling being prevented because of some rules.
     *
     * @param Move $move
     */
    public function __construct(Move $move)
    {
        parent::__construct(sprintf('%s castling is prevented.', $move->piece()));
    }
}
