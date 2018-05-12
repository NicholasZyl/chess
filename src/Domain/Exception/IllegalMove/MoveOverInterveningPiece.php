<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalMove;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;

final class MoveOverInterveningPiece extends IllegalMove
{
    /**
     * Create exception for move over intervening pieces.
     *
     * @param Coordinates $interveningPiecePosition
     */
    public function __construct(Coordinates $interveningPiecePosition)
    {
        parent::__construct(sprintf('Move is illegal because of intervening piece at %s', $interveningPiecePosition));
    }
}
