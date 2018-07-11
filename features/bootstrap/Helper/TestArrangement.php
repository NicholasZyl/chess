<?php
declare(strict_types=1);

namespace Helper;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\GameArrangement;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Rules;

class TestArrangement implements GameArrangement
{
    /**
     * @var GameArrangement
     */
    private $arrangement;

    /**
     * @var \SplObjectStorage|Coordinates[]|Piece[]
     */
    private $initialPositions;

    /**
     * Initialise board setup for test purposes.
     *
     * @param GameArrangement $arrangement
     */
    public function __construct(GameArrangement $arrangement)
    {
        $this->arrangement = $arrangement;
        $this->initialPositions = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): Rules
    {
        return $this->arrangement->rules();
    }

    /**
     * Plan placing piece at given coordinates.
     *
     * @param Piece $piece
     * @param Coordinates $at
     *
     * @return void
     */
    public function placePieceAt(Piece $piece, Coordinates $at): void
    {
        $this->initialPositions->attach($at, $piece);
    }

    /**
     * {@inheritdoc}
     */
    public function initialiseBoard(Board $board): void
    {
        if ($this->initialPositions->count() === 0) {
            $this->arrangement->initialiseBoard($board);
        } else {
            foreach ($this->initialPositions as $at) {
                $board->placePieceAt($this->initialPositions[$at], $at);
            }
        }
    }
}