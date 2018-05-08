<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Exception\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;

final class NotIntervened
{
    /**
     * @var Coordinates
     */
    private $source;

    /**
     * @var Coordinates
     */
    private $destination;

    /**
     * @var Board\Direction
     */
    private $direction;

    /**
     * @var Coordinates[]
     */
    private $steps;

    /**
     * Create move that cannot be done over any intervening pieces.
     *
     * @param Coordinates $source
     * @param Coordinates $destination
     * @param Board\Direction $direction
     */
    public function __construct(Coordinates $source, Coordinates $destination, Board\Direction $direction)
    {
        $this->source = $source;
        $this->destination = $destination;
        $this->direction = $direction;
        $this->steps = $this->planSteps($source, $destination, $direction);
    }

    /**
     * Plan all steps of the move.
     *
     * @param Coordinates $source
     * @param Coordinates $destination
     * @param Board\Direction $direction
     *
     * @return Coordinates[]
     */
    private function planSteps(Coordinates $source, Coordinates $destination, Board\Direction $direction): array
    {
        $steps = [];
        $step = $source->nextTowards($destination, $direction);
        while (!$step->equals($destination)) {
            $steps[] = $step;
            $step = $step->nextTowards($destination, $direction);
        }

        return $steps;
    }

    /**
     * Get the source coordinates.
     *
     * @return Coordinates
     */
    public function source(): Coordinates
    {
        return $this->source;
    }

    /**
     * Get the destination coordinates.
     *
     * @return Coordinates
     */
    public function destination(): Coordinates
    {
        return $this->destination;
    }

    /**
     * Get the move direction.
     *
     * @return Board\Direction
     */
    public function direction(): Board\Direction
    {
        return $this->direction;
    }

    /**
     * Get string representation of the move.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "not intervened move";
    }

    /**
     * Play the move on the board.
     *
     * @param Board $board
     *
     * @throws IllegalMove
     *
     * @return void
     */
    public function play(Board $board): void
    {
        try {
            foreach ($this->steps as $step) {
                $board->verifyThatPositionIsUnoccupied($step);
            }
        } catch (SquareIsOccupied $squareIsOccupied) {
            throw new MoveOverInterveningPiece($squareIsOccupied->coordinates());
        }

        $piece = $board->pickPieceFromCoordinates($this->source);
        $piece->canMove($this);
        $board->placePieceAtCoordinates($piece, $this->destination);
    }
}
