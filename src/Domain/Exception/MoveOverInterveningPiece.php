<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Move;

final class MoveOverInterveningPiece extends IllegalMove
{
    /**
     * Create exception for move over intervening pieces.
     *
     * @param Move $move
     * @param CoordinatePair $interveningPiecePosition
     */
    public function __construct(Move $move, CoordinatePair $interveningPiecePosition)
    {
        parent::__construct(sprintf('Move between %s and %s is illegal because of intervening piece at %s', $move->from(), $move->to(), $interveningPiecePosition));
    }
}
