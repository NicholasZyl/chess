<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board\Position\Coordinates;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;

abstract class ChessboardMove implements Move
{
    protected const INCREMENT = 1;
    protected const DECREMENT = -1;

    /**
     * @var CoordinatePair
     */
    private $from;

    /**
     * @var CoordinatePair
     */
    private $to;

    /**
     * @var CoordinatePair[]
     */
    private $steps;

    /**
     * @var int
     */
    private $stepIndex = 0;

    /**
     * Plan move along diagonal between two coordinates.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     *
     * @throws \InvalidArgumentException
     *
     * @return AlongDiagonal
     */
    public static function between(CoordinatePair $from, CoordinatePair $to): ChessboardMove
    {
        return new static($from, $to);
    }

    /**
     * ChessboardMove constructor.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     *
     * @throws \InvalidArgumentException
     */
    protected function __construct(CoordinatePair $from, CoordinatePair $to)
    {
        $this->validateIfMoveIsPossible($from, $to);
        $this->from = $from;
        $this->to = $to;
        $this->steps = $this->planSteps();
    }

    /**
     * Validate if move between two coordinates is possible.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    abstract protected function validateIfMoveIsPossible(CoordinatePair $from, CoordinatePair $to): void;

    /**
     * Plan all steps of the move.
     *
     * @return CoordinatePair[]
     */
    private function planSteps(): array
    {
        $steps = [];
        $step = $this->from;
        while (!$step->equals($this->to)) {
            $step = $this->nextCoordinatesTowards($step, $this->to);
            $steps[] = $step;
        }

        return $steps;
    }

    /**
     * Calculate next coordinate pair on the way between passed coordinates.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     *
     * @return CoordinatePair
     */
    abstract protected function nextCoordinatesTowards(CoordinatePair $from, CoordinatePair $to): CoordinatePair;

    /**
     * {@inheritdoc}
     */
    public function from(): Coordinates
    {
        return $this->from;
    }

    /**
     * {@inheritdoc}
     */
    public function to(): Coordinates
    {
        return $this->to;
    }

    /**
     * {@inheritdoc}
     */
    public function isTowardsOpponentSideFor(Color $color): bool
    {
        return $color->is(Color::white()) ? $this->from->rank() < $this->to->rank() : $this->to->rank() < $this->from->rank();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->steps[$this->stepIndex];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->stepIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->stepIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->steps[$this->key()]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->stepIndex = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->steps);
    }
}