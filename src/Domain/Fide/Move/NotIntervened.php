<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Move;

final class NotIntervened implements Move
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
     * @throws CoordinatesNotReachable
     */
    public function __construct(Coordinates $source, Coordinates $destination, Board\Direction $direction)
    {
        if (!$direction->areOnSame($source, $destination)) {
            throw new CoordinatesNotReachable($source, $destination, $direction);
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
    public function isOverDistanceOf(int $expectedDistance): bool
    {
        return $this->source->distanceTo($this->destination, $this->direction) === $expectedDistance;
    }

    /**
     * {@inheritdoc}
     */
    public function inDirection(Board\Direction $direction): bool
    {
        return $this->direction->inSameDirectionAs($direction);
    }

    /**
     * {@inheritdoc}
     */
    public function isLegal(Board $board): void
    {
        try {
            foreach ($this->steps as $step) {
                $board->verifyThatPositionIsUnoccupied($step);
            }
        } catch (SquareIsOccupied $squareIsOccupied) {
            throw new MoveOverInterveningPiece($squareIsOccupied->coordinates());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function play(Board $board): void
    {
        $this->isLegal($board);

        $piece = $board->pickPieceFromCoordinates($this->source);
        try {
            $piece->mayMove($this, $board);
            $board->placePieceAtCoordinates($piece, $this->destination);
        } catch (MoveNotAllowedForPiece $notAllowedForPiece) {
            $board->placePieceAtCoordinates($piece, $this->source);
            throw $notAllowedForPiece;
        } catch (SquareIsOccupied $squareIsOccupied) {
            $board->placePieceAtCoordinates($piece, $this->source);
            throw new MoveToOccupiedPosition($squareIsOccupied->coordinates());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('not intervened move %s', $this->direction);
    }
}
