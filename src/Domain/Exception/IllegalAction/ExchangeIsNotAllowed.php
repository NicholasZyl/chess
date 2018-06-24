<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Exception\IllegalAction;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;

final class ExchangeIsNotAllowed extends IllegalAction
{
    /**
     * @var Coordinates
     */
    private $position;

    /**
     * Create excpetion that exchange on given position was not possible.
     *
     * @param Coordinates $position
     */
    public function __construct(Coordinates $position)
    {
        $this->position = $position;
        parent::__construct(sprintf('Exchange piece on %s was not allowed.', $position));
    }

    /**
     * Get the position where exchange was to happen.
     *
     * @return Coordinates
     */
    public function position(): Coordinates
    {
        return $this->position;
    }
}
