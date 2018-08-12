<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Web;

use NicholasZyl\Chess\Application\Dto\Display;
use NicholasZyl\Chess\Application\Dto\GameDto;
use NicholasZyl\Chess\Application\Dto\PieceDto;

class JsonApiDisplay implements Display
{
    /**
     * {@inheritdoc}
     */
    public function visualiseGame(GameDto $game): string
    {
        return json_encode(
            [
                'board' => json_decode($game->board()->visualise($this)),
                'checked' => $game->checked(),
                'is_ended' => $game->isEnded(),
                'winner' => $game->winner(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function visualiseBoard(array $boardGrid): string
    {
        $board = [];

        /**
         * @var PieceDto[] $ranks
         */
        foreach ($boardGrid as $file => $ranks) {
            foreach ($ranks as $rank => $piece) {
                $board[$file][$rank] = $piece ? $piece->visualise($this) : '';
            }
        }

        return json_encode($board);
    }

    /**
     * {@inheritdoc}
     */
    public function visualisePiece(string $color, string $rank): string
    {
        return sprintf('%s %s', $color, $rank);
    }
}