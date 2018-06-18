<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Move;

trait NotIntervenedMove
{
    /**
     * Validate that move is not over any intervening pieces.
     *
     * @param Move $move
     * @param Game $game
     *
     * @return void
     */
    protected function validateNotIntervenedMove(Move $move, Game $game): void
    {
        $direction = $move->source()->directionTo($move->destination());
        $step = $move->source()->nextTowards($move->destination(), $direction);
        while (!$step->equals($move->destination())) {
            if ($game->isPositionOccupied($step)) {
                throw new MoveOverInterveningPiece($step);
            }
            $step = $step->nextTowards($move->destination(), $direction);
        }
    }
}