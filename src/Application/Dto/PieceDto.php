<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Application\Dto;

class PieceDto
{
    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $rank;

    /**
     * Create a data transfer object for a piece.
     *
     * @param string $color
     * @param string $rank
     */
    public function __construct(string $color, string $rank)
    {
        $this->color = $color;
        $this->rank = $rank;
    }

    /**
     * Get visual representation of the piece.
     *
     * @param Display $display
     *
     * @return string
     */
    public function visualise(Display $display): string
    {
        return $display->visualisePiece($this->color, $this->rank);
    }
}