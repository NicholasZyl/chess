<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Application\Dto;

use NicholasZyl\Chess\Domain\Piece;

final class BoardDto implements \JsonSerializable
{
    /**
     * @var string[][]
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
                $this->board[$file][$rank] = $piece ? sprintf('%s %s', strtolower((string)$piece->color()), (string)$piece) : '';
            }
        }
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
        return $this->board[$file][$rank];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->board;
    }
}
