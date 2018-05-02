<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece\Color;

final class Move
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
     * @var int
     */
    private $rankDistance;
    /**
     * @var int
     */
    private $fileDistance;

    /**
     * Distance constructor.
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
        $this->rankDistance = $to->rank() - $from->rank();
        $this->fileDistance = ord($to->file()) - ord($from->file());
    }

    /**
     * Calculates distance between two coordinates.
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
     * Checks if distance between starting and ending point is bigger than passed number of squares.
     *
     * @param int $numberOfSquares
     *
     * @return bool
     */
    public function isAwayMoreSquaresThan(int $numberOfSquares)
    {
        return abs($this->rankDistance) > $numberOfSquares || abs($this->fileDistance) > $numberOfSquares;
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
     * Checks if move is made forward for given color.
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

    public function steps(): array
    {
        // TODO: write logic here
    }
}
