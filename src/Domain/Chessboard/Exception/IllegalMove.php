<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Exception;

use NicholasZyl\Chess\Domain\Chessboard\ChessboardMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class IllegalMove extends NotPermittedMove
{
    /**
     * IllegalMove constructor.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     */
    public function __construct(CoordinatePair $from, CoordinatePair $to)
    {
        parent::__construct(sprintf('Move from %s to %s is illegal', $from, $to));
    }

    public static function forMove(ChessboardMove $move): IllegalMove
    {
        return new self(CoordinatePair::fromFileAndRank('a', 1), CoordinatePair::fromFileAndRank('a', 1)); //TODO: Fake value until refactoring
    }
}
