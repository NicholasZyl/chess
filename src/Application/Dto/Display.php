<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Application\Dto;

interface Display
{
    /**
     * Represent the game visually.
     *
     * @param GameDto $game
     *
     * @return string
     */
    public function visualiseGame(GameDto $game): string;

    /**
     * Represent the board visually.
     *
     * @param PieceDto[][] $boardGrid
     *
     * @return string
     */
    public function visualiseBoard(array $boardGrid): string;

    /**
     * Represent the piece visually.
     *
     * @param string $color
     * @param string $rank
     *
     * @return string
     */
    public function visualisePiece(string $color, string $rank): string;
}