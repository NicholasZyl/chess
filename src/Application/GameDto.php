<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Application;

use NicholasZyl\Chess\Domain\Piece;

final class GameDto
{
    /**
     * @var Piece[][]|null[][]
     */
    private $board;

    /**
     * Create Data Transfer Object for the game.
     *
     * @param Piece[][]|null[][] $board
     */
    public function __construct(array $board)
    {
        $this->board = $board;
    }

    /**
     * Get piece at given position.
     *
     * @param string $file
     * @param int $rank
     *
     * @return string
     */
    public function position(string $file, int $rank): string
    {
        $piece = $this->board[$file][$rank];
        if (!$piece) {
            return '';
        }

        return sprintf('%s %s', strtolower((string)$piece->color()), (string)$piece);
    }
}
