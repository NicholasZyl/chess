<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Move;

class MoveToIllegalPosition extends IllegalAction
{
    /**
     * Create exception for move that would be made to an illegal position for given piece.
     *
     * @param Move $move
     */
    public function __construct(Move $move)
    {
        parent::__construct(sprintf('%s is illegal position for %s standing at %s', $move->destination(), $move->piece(), $move->source()));
    }
}