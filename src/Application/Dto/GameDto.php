<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Application\Dto;

final class GameDto
{
    /**
     * @var BoardDto
     */
    private $boardDto;

    /**
     * @var bool
     */
    private $isEnded;

    /**
     * @var string|null
     */
    private $winner;

    /**
     * @var string|null
     */
    private $checked;

    /**
     * Create a data transfer object for the game.
     *
     * @param BoardDto $boardDto
     * @param string|null $checked
     * @param bool $isEnded
     * @param string|null $winner
     */
    public function __construct(BoardDto $boardDto, ?string $checked = null, bool $isEnded = false, ?string $winner = null)
    {
        $this->boardDto = $boardDto;
        $this->isEnded = $isEnded;
        $this->winner = $winner;
        $this->checked = $checked;
    }

    /**
     * Get board data transfer object.
     *
     * @return BoardDto
     */
    public function board(): BoardDto
    {
        return $this->boardDto;
    }

    /**
     * Has the game already ended.
     *
     * @return bool
     */
    public function isEnded(): bool
    {
        return $this->isEnded;
    }

    /**
     * Get the player who is currently checked.
     *
     * @return string|null
     */
    public function checked(): ?string
    {
        return $this->checked;
    }

    /**
     * Get the winner of the game.
     *
     * @return string|null
     */
    public function winner(): ?string
    {
        return $this->winner;
    }

    /**
     * Get visual representation of the game.
     *
     * @param Display $display
     *
     * @return string
     */
    public function visualise(Display $display): string
    {
        return $display->visualiseGame($this);
    }
}
