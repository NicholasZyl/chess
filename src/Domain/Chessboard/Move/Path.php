<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;

final class Path implements \Iterator
{
    /**
     * @var CoordinatePair[]
     */
    private $coordinates;

    private $index = 0;

    private function __construct(array $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    public static function forSquares(array $coordinates)
    {
        return new Path($coordinates);
    }

    /**
     * {@inheritdoc}
     *
     * @return CoordinatePair
     */
    public function current()
    {
        return $this->coordinates[$this->index];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->coordinates[$this->key()]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->index = 0;
    }
}
