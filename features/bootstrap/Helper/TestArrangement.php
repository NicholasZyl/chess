<?php
declare(strict_types=1);

namespace Helper;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\KingCheck;
use NicholasZyl\Chess\Domain\Fide\Rules\KingMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\Turns;
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
     * @var Rules
     */
    private $rules;

    /**
     * @var Turns|null
     */
    private $turn;

    /**
     * @var Coordinates[]
     */
    private $kingsPositions = [];

    /**
     * @var Coordinates[]
     */
    private $rooksPositions = [];

    /**
     * Initialise board setup for test purposes.
     *
     * @param GameArrangement $arrangement
     */
    public function __construct(GameArrangement $arrangement)
    {
        $this->arrangement = $arrangement;
        $this->rules = $this->arrangement->rules();
        $this->initialPositions = new \SplObjectStorage();
        $this->kingsPositions = [
            Color::WHITE => CoordinatePair::fromFileAndRank('e', 1),
            Color::BLACK => CoordinatePair::fromFileAndRank('e', 8),
        ];
    }

    /**
     * Set which color's turn it is.
     *
     * @param Color $color
     *
     * @return void
     */
    public function setTurn(Color $color): void
    {
        $this->turn = new Turns($color);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): Rules
    {
        $replacedRules = [];
        if ($this->turn) {
            $replacedRules[] = $this->turn;
        }
        if (!empty($this->kingsPositions)) {
            $replacedRules[] = new KingCheck($this->kingsPositions[Color::WHITE], $this->kingsPositions[Color::BLACK]);
        }
        if (!empty($this->rooksPositions)) {
            $replacedRules[] = new KingMoves($this->rooksPositions);
        }

        return $this->rules->replace($replacedRules);
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
        if ($piece instanceof Rook) {
            $this->rooksPositions[] = $at;
        } elseif ($piece instanceof King) {
            $this->kingsPositions[(string)$piece->color()] = $at;
        }
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