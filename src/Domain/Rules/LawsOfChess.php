<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules;

final class LawsOfChess implements Rules
{
    /**
     * @var RankMovementRules[]
     */
    private $piecesMovementsRules;

    /**
     * Chess constructor.
     *
     * @param Rank[] $allPossibleRanks
     * @param RankMovementRules[] $piecesMovementsRules
     */
    public function __construct(array $allPossibleRanks, array $piecesMovementsRules)
    {
        foreach ($piecesMovementsRules as $pieceMovementsRules) {
            $this->addRankMovementRules($pieceMovementsRules);
        }
        $missingPiecesRules = array_diff($allPossibleRanks, array_keys($this->piecesMovementsRules));
        if (!empty($missingPiecesRules)) {
            throw new Exception\IncompleteRules($missingPiecesRules);
        }
    }

    /**
     * Add movement rules for rank.
     *
     * @param RankMovementRules $pieceMovementRules
     *
     * @return void
     */
    private function addRankMovementRules(RankMovementRules $pieceMovementRules): void
    {
        $this->piecesMovementsRules[(string) $pieceMovementRules->isFor()] = $pieceMovementRules;
    }

    /**
     * {@inheritdoc}
     */
    public function validateMove(Square $from, Square $to): void
    {
        $piece = $from->peek();

        if (!array_key_exists((string) $piece->rank(), $this->piecesMovementsRules)) {
            throw new Exception\MissingRule($piece->rank());
        }
        $this->piecesMovementsRules[(string) $piece->rank()]->validate($from->coordinates(), $to->coordinates());
    }
}
