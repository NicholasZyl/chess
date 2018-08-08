<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Chessboard;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece\Rook;
use NicholasZyl\Chess\Domain\Rules\BishopMoves;
use NicholasZyl\Chess\Domain\Rules\GameCompletion;
use NicholasZyl\Chess\Domain\Rules\KingCheck;
use NicholasZyl\Chess\Domain\Rules\KingMoves;
use NicholasZyl\Chess\Domain\Rules\KnightMoves;
use NicholasZyl\Chess\Domain\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Rules\QueenMoves;
use NicholasZyl\Chess\Domain\Rules\RookMoves;
use NicholasZyl\Chess\Domain\Rules\Turns;

final class LawsOfChess implements GameArrangement
{
    private const WHITE_KINGSIDE_ROOK = 0;
    private const WHITE_QUEENSIDE_ROOK = 1;
    private const BLACK_KINGSIDE_ROOK = 2;
    private const BLACK_QUEENSIDE_ROOK = 3;

    /**
     * @var CoordinatePair
     */
    private $whiteKingInitialPosition;

    /**
     * @var CoordinatePair
     */
    private $blackKingInitialPosition;

    /**
     * @var CoordinatePair[]
     */
    private $rooksPositions;

    /**
     * Prepare Laws of Chess rules set.
     */
    public function __construct()
    {
        $this->whiteKingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $this->blackKingInitialPosition = CoordinatePair::fromFileAndRank('e', 8);
        $this->rooksPositions = [
            self::WHITE_KINGSIDE_ROOK => CoordinatePair::fromFileAndRank('a', 1),
            self::WHITE_QUEENSIDE_ROOK => CoordinatePair::fromFileAndRank('h', 1),
            self::BLACK_KINGSIDE_ROOK => CoordinatePair::fromFileAndRank('a', 8),
            self::BLACK_QUEENSIDE_ROOK => CoordinatePair::fromFileAndRank('h', 8),
        ];
    }

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
                new KingMoves(array_values($this->rooksPositions)),
                new KingCheck($this->whiteKingInitialPosition, $this->blackKingInitialPosition),
                new Turns(Color::white()),
                new GameCompletion(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initialiseBoard(Board $board): void
    {
        $board->placePieceAt(Rook::forColor(Color::white()), $this->rooksPositions[self::WHITE_KINGSIDE_ROOK]);
        $board->placePieceAt(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 1));
        $board->placePieceAt(Bishop::forColor(Color::white()), CoordinatePair::fromFileAndRank('c', 1));
        $board->placePieceAt(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('d', 1));
        $board->placePieceAt(King::forColor(Color::white()), $this->whiteKingInitialPosition);
        $board->placePieceAt(Bishop::forColor(Color::white()), CoordinatePair::fromFileAndRank('f', 1));
        $board->placePieceAt(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('g', 1));
        $board->placePieceAt(Rook::forColor(Color::white()), $this->rooksPositions[self::WHITE_QUEENSIDE_ROOK]);
        for ($file = Chessboard::FILE_MOST_QUEENSIDE; $file <= Chessboard::FILE_MOST_KINGSIDE; ++$file) {
            $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank($file, 2));
        }

        $board->placePieceAt(Rook::forColor(Color::black()), $this->rooksPositions[self::BLACK_KINGSIDE_ROOK]);
        $board->placePieceAt(Knight::forColor(Color::black()), CoordinatePair::fromFileAndRank('b', 8));
        $board->placePieceAt(Bishop::forColor(Color::black()), CoordinatePair::fromFileAndRank('c', 8));
        $board->placePieceAt(Queen::forColor(Color::black()), CoordinatePair::fromFileAndRank('d', 8));
        $board->placePieceAt(King::forColor(Color::black()), $this->blackKingInitialPosition);
        $board->placePieceAt(Bishop::forColor(Color::black()), CoordinatePair::fromFileAndRank('f', 8));
        $board->placePieceAt(Knight::forColor(Color::black()), CoordinatePair::fromFileAndRank('g', 8));
        $board->placePieceAt(Rook::forColor(Color::black()), $this->rooksPositions[self::BLACK_QUEENSIDE_ROOK]);
        for ($file = Chessboard::FILE_MOST_QUEENSIDE; $file <= Chessboard::FILE_MOST_KINGSIDE; ++$file) {
            $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank($file, 7));
        }
    }
}
