<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveOverInterveningPiece;

trait NotIntervenedMove
{
    /**
     * Validate that move is not over any intervening pieces.
     *
     * @param Move $move
     * @param Board $board
     *
     * @return void
     */
    protected function validateNotIntervenedMove(Move $move, Board $board): void
    {
        $direction = $move->source()->directionTo($move->destination());
        $step = $move->source()->nextTowards($move->destination(), $direction);
        while (!$step->equals($move->destination())) {
            if ($board->isPositionOccupied($step)) {
                throw new MoveOverInterveningPiece($step);
            }
            $step = $step->nextTowards($move->destination(), $direction);
        }
    }
}