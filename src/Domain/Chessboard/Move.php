<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece\Color;

final class Move implements \Iterator, \Countable
{
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
    private $path;

    /**
     * @var int
     */
    private $pathIndex = 0;

    /**
     * Plan move between two coordinate pairs.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     *
     * @throws \InvalidArgumentException
     *
     * @return Move
     */
    public static function between(CoordinatePair $from, CoordinatePair $to): Move
    {
        return new Move($from, $to);
    }

    /**
     * Move constructor.
     *
     * @param CoordinatePair $from
     * @param CoordinatePair $to
     *
     * @throws \InvalidArgumentException
     */
    private function __construct(CoordinatePair $from, CoordinatePair $to)
    {
        if ($from->equals($to)) {
            throw new \InvalidArgumentException('It is not possible to move to the same square.');
        }
        $this->from = $from;
        $this->to = $to;
        $this->path = $this->planPath();
    }

    /**
     * Prepare path for planned move.
     *
     * @return CoordinatePair[]
     */
    private function planPath(): array
    {
        $steps = [];
        $step = $this->from;
        while (!$step->equals($this->to)) {
            $step = $step->nextCoordinatesTowards($this->to);
            $steps[] = $step;
        }

        return $steps;
    }

    /**
     * Checks if distance between starting and ending point is bigger than passed number of squares.
     *
     * @param int $numberOfSquares
     *
     * @return bool
     */
    public function isAwayMoreSquaresThan(int $numberOfSquares)
    {
        return count($this->path) > $numberOfSquares;
    }

    /**
     * Checks if move is made along file.
     *
     * @return bool
     */
    public function isAlongFile(): bool
    {
        return $this->from->isOnSameFile($this->to);
    }

    /**
     * Checks if move is made along rank.
     *
     * @return bool
     */
    public function isAlongRank(): bool
    {
        return $this->from->isOnSameRank($this->to);
    }

    /**
     * Checks if move is made along diagonal.
     *
     * @return bool
     */
    public function isAlongDiagonal(): bool
    {
        return $this->from->isOnSameDiagonal($this->to);
    }

    /**
     * Checks if move is made towards the opposite side for given color.
     *
     * @param Color $color
     *
     * @return bool
     */
    public function isForward(Color $color): bool
    {
        $hasDestinationSquareHigherRank = $this->to->hasHigherRankThan($this->from);

        return $color->is(Color::white()) ? $hasDestinationSquareHigherRank : !$hasDestinationSquareHigherRank;
    }

    /**
     * {@inheritdoc}
     * @return CoordinatePair
     */
    public function current()
    {
        return $this->path[$this->pathIndex];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->pathIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->pathIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->path[$this->pathIndex]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->pathIndex = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->path);
    }
}
