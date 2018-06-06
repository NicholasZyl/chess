<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;

final class Rules
{
    /**
     * @var PieceMoves[]
     */
    private $rules;

    /**
     * Create set of rules.
     *
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Check if move can be played for given piece.
     *
     * @param Piece $piece
     * @param Move $move
     *
     * @throws IllegalMove
     *
     * @return void
     */
    public function mayMove(Piece $piece, Move $move): void
    {
        foreach ($this->rules as $rule) {
            if ($rule->isApplicableFor($piece)) {
                $rule->mayMove($piece, $move);
            }
        }
    }

    /**
     * Apply all rules after event happened.
     *
     * @param Event $event
     *
     * @return void
     */
    public function applyAfter(Event $event): void
    {
        foreach ($this->rules as $rule) {
            $rule->applyAfter($event);
        }
    }
}
