<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Exception;

abstract class NotPermittedMove extends \RuntimeException
{
    /**
     * InvalidMove constructor.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}