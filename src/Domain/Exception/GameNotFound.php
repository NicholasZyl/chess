<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception;

use NicholasZyl\Chess\Domain\GameId;

final class GameNotFound extends \RuntimeException
{
    /**
     * @var GameId
     */
    private $gameId;

    /**
     * Create an exception for not found game.
     *
     * @param GameId $gameId
     */
    public function __construct(GameId $gameId)
    {
        $this->gameId = $gameId;
        parent::__construct('Game was not found.');
    }

    /**
     * Get the identifier of a game that was nto found.
     *
     * @return GameId
     */
    public function id(): GameId
    {
        return $this->gameId;
    }
}
