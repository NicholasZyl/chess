<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Piece;

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

    /**
     * Get all possible destinations in given directions not over intervening pieces.
     *
     * @param array $directions
     * @param Coordinates $actualPosition
     * @param Board $board
     * @param Piece $piece
     *
     * @return \Generator
     */
    protected function getNotIntervenedDestinationsForDirections(array $directions, Coordinates $actualPosition, Board $board, Piece $piece): \Generator
    {
        foreach ($directions as $direction) {
            $step = $actualPosition;
            do {
                try {
                    $step = $direction->nextAlongFrom($step);

                    if ($board->isPositionOccupied($step)) {
                        if (!$board->isPositionOccupiedBy($step, $piece->color())) {
                            yield $step;
                        }
                        $step = null;
                    } else {
                        yield $step;
                    }
                } catch (OutOfBoard $outOfBoard) {
                    $step = null;
                }
            } while ($step);
        }
    }
}