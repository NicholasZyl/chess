<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;

final class CastlingPrevented extends IllegalAction
{
    /**
     * Create an exception for castling being prevented because of some rules.
     *
     * @param Move $move
     */
    public function __construct(Move $move)
    {
        parent::__construct(sprintf('%s castling is prevented.', $move->piece()));
    }
}
