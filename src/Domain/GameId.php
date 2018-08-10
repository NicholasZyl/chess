<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

final class GameId
{
    /**
     * @var string
     */
    private $id;

    /**
     * Generate new random identifier.
     *
     * @return GameId
     */
    public static function generate()
    {
        return new GameId(uniqid('game-', true));
    }

    /**
     * Create game identifier from string.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Get the identifier.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Represent as a string.
     */
    public function __toString(): string
    {
        return $this->id();
    }
}
