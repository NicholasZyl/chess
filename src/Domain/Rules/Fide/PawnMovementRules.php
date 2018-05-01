<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules\Fide;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\MovementRules;

final class PawnMovementRules implements MovementRules
{
    private const STANDARD_ALLOWED_DISTANCE = 1;
    private const FIRST_MOVE_ALLOWED_DISTANCE = 2;
    private const INITIAL_RANK_FOR_WHITES = 2;
    private const INITIAL_RANK_FOR_BLACKS = 7;

    /**
     * {@inheritdoc}
     */
    public function forRank(): Rank
    {
        return Rank::pawn();
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Color $color, Coordinates $from, Coordinates $to): void
    {
        $initialRank = $color->isSameAs(Color::white()) ? self::INITIAL_RANK_FOR_WHITES : self::INITIAL_RANK_FOR_BLACKS;
        $allowedDistance = $from->rank() === $initialRank ? self::FIRST_MOVE_ALLOWED_DISTANCE : self::STANDARD_ALLOWED_DISTANCE;

        $distance = $from->distance($to);
        if (!$distance->isVertical() || $distance->isHigherThan($allowedDistance) || !$distance->isForward($color)) {
            throw new IllegalMove($from, $to);
        }
    }
}
