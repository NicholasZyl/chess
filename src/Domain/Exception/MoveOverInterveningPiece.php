<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Board\Position\Coordinates;
use NicholasZyl\Chess\Domain\Move;

final class MoveOverInterveningPiece extends IllegalMove
{
    /**
     * Create exception for move over intervening pieces.
     *
     * @param Move $move
     * @param Coordinates $interveningPiecePosition
     */
    public function __construct(Move $move, Coordinates $interveningPiecePosition)
    {
        parent::__construct(sprintf('Move between %s and %s is illegal because of intervening piece at %s', $move->from(), $move->to(), $interveningPiecePosition));
    }
}
