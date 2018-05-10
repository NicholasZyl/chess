<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Move;

class NotIntervened implements Move
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
     *
     * @throws InvalidDirection
     */
    public function __construct(Coordinates $source, Coordinates $destination, Board\Direction $direction)
    {
        if (!$direction->areOnSame($source, $destination)) {
            throw new InvalidDirection($source, $destination, $direction);
        }

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
     * {@inheritdoc}
     */
    public function source(): Coordinates
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function destination(): Coordinates
    {
        return $this->destination;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('not intervened move %s', $this->direction);
    }

    /**
     * {@inheritdoc}
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
        $piece->mayMove($this);
        $board->placePieceAtCoordinates($piece, $this->destination);
    }

    /**
     * {@inheritdoc}
     */
    public function is(string $moveType): bool
    {
        return $this instanceof $moveType;
    }

    /**
     * {@inheritdoc}
     */
    public function inDirection(Board\Direction $direction): bool
    {
        return $this->direction->inSameDirectionAs($direction);
    }
}
