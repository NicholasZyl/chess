<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Console;

use NicholasZyl\Chess\Application\Dto\Display;
use NicholasZyl\Chess\Application\Dto\GameDto;
use NicholasZyl\Chess\Domain\Color;

class AsciiTerminalDisplay implements Display
{
    /**
     * {@inheritdoc}
     */
    public function visualiseGame(GameDto $game): string
    {
        return $game->board()->visualise($this);
    }

    /**
     * {@inheritdoc}
     */
    public function visualiseBoard(array $boardGrid): string
    {
        $board = '  -------------------'.PHP_EOL;

        for ($rank = 8; $rank >= 1; --$rank) {
            $board .= $rank.' | ';
            for ($file = 'a'; $file <= 'h'; ++$file) {
                $board .= $boardGrid[$file][$rank] ? $boardGrid[$file][$rank]->visualise($this) : ' ';
                $board .= ' ';
            }
            $board .= '|'.PHP_EOL;
        }
        $board .= '  -------------------'.PHP_EOL;
        $board .= '    A B C D E F G H'.PHP_EOL;

        return $board;
    }

    /**
     * {@inheritdoc}
     */
    public function visualisePiece(string $color, string $rank): string
    {
        $letter = $rank === 'knight' ? 'n' : $rank[0];

        return $color === Color::WHITE ? strtoupper($letter) : strtolower($letter);
    }
}