<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide\Piece;

final class Queen extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'queen';
    }
}
