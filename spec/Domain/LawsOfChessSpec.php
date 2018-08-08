<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\GameArrangement;
use NicholasZyl\Chess\Domain\LawsOfChess;
use NicholasZyl\Chess\Domain\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece\Rook;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\BishopMoves;
use NicholasZyl\Chess\Domain\Rules\GameCompletion;
use NicholasZyl\Chess\Domain\Rules\KingCheck;
use NicholasZyl\Chess\Domain\Rules\KingMoves;
use NicholasZyl\Chess\Domain\Rules\KnightMoves;
use NicholasZyl\Chess\Domain\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Rules\QueenMoves;
use NicholasZyl\Chess\Domain\Rules\RookMoves;
use NicholasZyl\Chess\Domain\Rules\Turns;
use PhpSpec\ObjectBehavior;

class LawsOfChessSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LawsOfChess::class);
    }

    function it_is_a_game_arrangement()
    {
        $this->shouldBeAnInstanceOf(GameArrangement::class);
    }

    function it_has_all_rules_defined_in_fide_handbook()
    {
        $this->rules()->shouldBeLike(
            new Rules([
                new PawnMoves(),
                new KnightMoves(),
                new BishopMoves(),
                new RookMoves(),
                new QueenMoves(),
                new KingMoves(
                    [
                        CoordinatePair::fromFileAndRank('a', 1),
                        CoordinatePair::fromFileAndRank('h', 1),
                        CoordinatePair::fromFileAndRank('a', 8),
                        CoordinatePair::fromFileAndRank('h', 8),
                    ]
                ),
                new KingCheck(
                    CoordinatePair::fromFileAndRank('e', 1),
                    CoordinatePair::fromFileAndRank('e', 8)
                ),
                new Turns(),
                new GameCompletion(),
            ])
        );
    }

    function it_places_all_16_white_and_16_black_pieces_on_their_positions(Board $board)
    {
        $board->placePieceAt(Rook::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 1))->shouldBeCalled();
        $board->placePieceAt(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 1))->shouldBeCalled();
        $board->placePieceAt(Bishop::forColor(Color::white()), CoordinatePair::fromFileAndRank('c', 1))->shouldBeCalled();
        $board->placePieceAt(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('d', 1))->shouldBeCalled();
        $board->placePieceAt(King::forColor(Color::white()), CoordinatePair::fromFileAndRank('e', 1))->shouldBeCalled();
        $board->placePieceAt(Bishop::forColor(Color::white()), CoordinatePair::fromFileAndRank('f', 1))->shouldBeCalled();
        $board->placePieceAt(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('g', 1))->shouldBeCalled();
        $board->placePieceAt(Rook::forColor(Color::white()), CoordinatePair::fromFileAndRank('h', 1))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 2))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 2))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('c', 2))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('d', 2))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('e', 2))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('f', 2))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('g', 2))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('h', 2))->shouldBeCalled();

        $board->placePieceAt(Rook::forColor(Color::black()), CoordinatePair::fromFileAndRank('a', 8))->shouldBeCalled();
        $board->placePieceAt(Knight::forColor(Color::black()), CoordinatePair::fromFileAndRank('b', 8))->shouldBeCalled();
        $board->placePieceAt(Bishop::forColor(Color::black()), CoordinatePair::fromFileAndRank('c', 8))->shouldBeCalled();
        $board->placePieceAt(Queen::forColor(Color::black()), CoordinatePair::fromFileAndRank('d', 8))->shouldBeCalled();
        $board->placePieceAt(King::forColor(Color::black()), CoordinatePair::fromFileAndRank('e', 8))->shouldBeCalled();
        $board->placePieceAt(Bishop::forColor(Color::black()), CoordinatePair::fromFileAndRank('f', 8))->shouldBeCalled();
        $board->placePieceAt(Knight::forColor(Color::black()), CoordinatePair::fromFileAndRank('g', 8))->shouldBeCalled();
        $board->placePieceAt(Rook::forColor(Color::black()), CoordinatePair::fromFileAndRank('h', 8))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('a', 7))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('b', 7))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('c', 7))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('d', 7))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('e', 7))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('f', 7))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('g', 7))->shouldBeCalled();
        $board->placePieceAt(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('h', 7))->shouldBeCalled();

        $this->initialiseBoard($board);
    }
}
