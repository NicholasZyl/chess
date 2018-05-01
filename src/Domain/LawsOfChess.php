<?php

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\Piece\Rank;
use NicholasZyl\Chess\Domain\Rules\Exception\IncompleteRules;
use NicholasZyl\Chess\Domain\Rules\Exception\MissingRule;
use NicholasZyl\Chess\Domain\Rules\Fide\BishopMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\KingMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\KnightMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\PawnMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\QueenMovementRules;
use NicholasZyl\Chess\Domain\Rules\Fide\RookMovementRules;
use NicholasZyl\Chess\Domain\Rules\MovementRules;

class LawsOfChess
{
    /**
     * @var MovementRules[]
     */
    private $movementRules;

    /**
     * LawsOfChess constructor.
     *
     * @param Rank[] $allPossibleRanks
     * @param MovementRules[] $piecesMovementsRules
     */
    public function __construct(array $allPossibleRanks, array $piecesMovementsRules)
    {
        foreach ($piecesMovementsRules as $pieceMovementsRules) {
            $this->addRankMovementRules($pieceMovementsRules);
        }
        $missingPiecesRules = array_diff($allPossibleRanks, array_keys((array) $this->movementRules));
        if (!empty($missingPiecesRules)) {
            throw new IncompleteRules($missingPiecesRules);
        }
    }

    /**
     * Prepare standard set of rules from FIDE handbook.
     * @see https://www.fide.com/fide/handbook.html?id=208&view=article
     *
     * @return LawsOfChess
     */
    public static function fromFideHandbook(): LawsOfChess
    {
        return new LawsOfChess(
            Rank::availableRanks(),
            [
                new KingMovementRules(),
                new QueenMovementRules(),
                new RookMovementRules(),
                new BishopMovementRules(),
                new KnightMovementRules(),
                new PawnMovementRules(),
            ]
        );
    }

    /**
     * Add movement rules for rank.
     *
     * @param MovementRules $pieceMovementRules
     *
     * @return void
     */
    private function addRankMovementRules(MovementRules $pieceMovementRules): void
    {
        $this->movementRules[(string) $pieceMovementRules->forRank()] = $pieceMovementRules;
    }
    /**
     * Validate if proposed move is valid according to the game's rules.
     *
     * @param Square $from
     * @param Square $to
     *
     * @throws MissingRule
     * @throws IllegalMove
     *
     * @return void
     */
    public function validateMove(Square $from, Square $to): void
    {
        $piece = $from->peek();

        if (!array_key_exists((string) $piece->rank(), $this->movementRules)) {
            throw new MissingRule($piece->rank());
        }
        $this->movementRules[(string) $piece->rank()]->validate($piece->color(), $from->coordinates(), $to->coordinates());
    }

    /**
     * List all movement rules.
     *
     * @return array
     */
    public function listMovementRules()
    {
        return array_values($this->movementRules);
    }
}
