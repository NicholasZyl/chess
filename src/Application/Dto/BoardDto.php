<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Application\Dto;

use NicholasZyl\Chess\Domain\Piece;

final class BoardDto
{
    /**
     * @var PieceDto[][]
     */
    private $board;

    /**
     * Create Data Transfer Object for the board.
     *
     * @param Piece[][]|null[][] $board
     */
    public function __construct(array $board)
    {
        foreach ($board as $file => $ranks) {
            foreach ($ranks as $rank => $piece) {
                $this->board[$file][$rank] = $piece ? new PieceDto((string)$piece->color(), (string)$piece) : '';
            }
        }
    }

    /**
     * Get piece at given position.
     *
     * @param string $file
     * @param int $rank
     *
     * @return PieceDto
     */
    public function position(string $file, int $rank): PieceDto
    {
        return $this->board[$file][$rank];
    }

    /**
     * Get visual representation of the board.
     *
     * @param Display $display
     *
     * @return string
     */
    public function visualise(Display $display): string
    {
        return $display->visualiseBoard($this->board);
    }
}
