<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Chessboard\Rules;

use NicholasZyl\Chess\Domain\Chessboard\Rules;
use NicholasZyl\Chess\Domain\Chessboard\Square;

final class Chess implements Rules
{
    /**
     * @var PieceMovementRules[]
     */
    private $piecesMovementsRules;

    public function __construct(array $piecesMovementsRules)
    {
        foreach ($piecesMovementsRules as $pieceMovementsRules) {
            $this->addPieceMovementRules($pieceMovementsRules);
        }
    }

    private function addPieceMovementRules(PieceMovementRules $pieceMovementRules): void
    {
        $this->piecesMovementsRules[(string) $pieceMovementRules->isFor()] = $pieceMovementRules;
    }

    /**
     * {@inheritdoc}
     */
    public function validateMove(Square $from, Square $to): void
    {
        $piece = $from->peek();
        $this->piecesMovementsRules[(string) $piece->rank()]->validate($from->coordinates(), $to->coordinates());
    }
}
