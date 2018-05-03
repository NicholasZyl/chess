<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class MoveAlongDiagonal implements \Iterator, \Countable
{
    private const INCREMENT = 1;
    private const DECREMENT = -1;

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
     * @return MoveAlongDiagonal
     */
    public static function between(CoordinatePair $from, CoordinatePair $to): MoveAlongDiagonal
    {
        return new MoveAlongDiagonal($from, $to);
    }

    /**
     * DiagonalMove constructor.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     *
     * @throws \InvalidArgumentException
     */
    private function __construct(CoordinatePair $from, CoordinatePair $to)
    {
        if (!$from->isOnSameDiagonal($to)) {
            throw new \InvalidArgumentException(
                sprintf('%s and %s are not along the same diagonal.', $from, $to)
            );
        }
        $this->from = $from;
        $this->to = $to;
        $this->steps = $this->planSteps();
    }

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
    private function nextCoordinatesTowards(CoordinatePair $from, CoordinatePair $to): CoordinatePair
    {
        $isTowardsHigherFile = ord($from->file()) < ord($to->file());
        $isTowardsHigherRank = $from->rank() < $to->rank();

        return CoordinatePair::fromFileAndRank(
            chr((ord($from->file()) + ($isTowardsHigherFile ? self::INCREMENT : self::DECREMENT))),
            $from->rank() + ($isTowardsHigherRank ? self::INCREMENT : self::DECREMENT)
        );
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
