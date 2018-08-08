<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Piece;

final class Pawn extends Piece
{
    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return 'pawn';
    }
}
