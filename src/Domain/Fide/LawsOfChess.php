<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\BishopMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\KingCheck;
use NicholasZyl\Chess\Domain\Fide\Rules\KingMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\KnightMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\QueenMoves;
use NicholasZyl\Chess\Domain\Fide\Rules\RookMoves;
use NicholasZyl\Chess\Domain\GameArrangement;
use NicholasZyl\Chess\Domain\Rules;

final class LawsOfChess implements GameArrangement
{
    /**
     * {@inheritdoc}
     */
    public function rules(): Rules
    {
        return new Rules(
            [
                new PawnMoves(),
                new KnightMoves(),
                new BishopMoves(),
                new RookMoves(),
                new QueenMoves(),
                new KingMoves(),
                new KingCheck(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initialiseBoard(Board $board): void
    {
        $board->placePieceAt(Rook::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 1));
        $board->placePieceAt(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 1));
        $board->placePieceAt(Bishop::forColor(Color::white()), CoordinatePair::fromFileAndRank('c', 1));
        $board->placePieceAt(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('d', 1));
        $board->placePieceAt(King::forColor(Color::white()), CoordinatePair::fromFileAndRank('e', 1));
        $board->placePieceAt(Bishop::forColor(Color::white()), CoordinatePair::fromFileAndRank('f', 1));
        $board->placePieceAt(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('g', 1));
        $board->placePieceAt(Rook::forColor(Color::white()), CoordinatePair::fromFileAndRank('h', 1));
        for ($file = 'a'; $file <= 'h'; ++$file) {
            $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank($file, 2));
        }

        $board->placePieceAt(Rook::forColor(Color::black()), CoordinatePair::fromFileAndRank('a', 8));
        $board->placePieceAt(Knight::forColor(Color::black()), CoordinatePair::fromFileAndRank('b', 8));
        $board->placePieceAt(Bishop::forColor(Color::black()), CoordinatePair::fromFileAndRank('c', 8));
        $board->placePieceAt(Queen::forColor(Color::black()), CoordinatePair::fromFileAndRank('d', 8));
        $board->placePieceAt(King::forColor(Color::black()), CoordinatePair::fromFileAndRank('e', 8));
        $board->placePieceAt(Bishop::forColor(Color::black()), CoordinatePair::fromFileAndRank('f', 8));
        $board->placePieceAt(Knight::forColor(Color::black()), CoordinatePair::fromFileAndRank('g', 8));
        $board->placePieceAt(Rook::forColor(Color::black()), CoordinatePair::fromFileAndRank('h', 8));
        for ($file = 'a'; $file <= 'h'; ++$file) {
            $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank($file, 7));
        }
    }
}
