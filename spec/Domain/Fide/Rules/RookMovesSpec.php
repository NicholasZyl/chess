<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\RookMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;
use PhpSpec\ObjectBehavior;

class RookMovesSpec extends ObjectBehavior
{
    /**
     * @var Rook
     */
    private $rook;

    function let()
    {
        $this->rook = Rook::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RookMoves::class);
    }

    function it_is_piece_moves_rule()
    {
        $this->shouldBeAnInstanceOf(PieceMoves::class);
    }

    function it_is_applicable_for_king()
    {
        $this->areApplicableFor($this->rook)->shouldBe(true);
    }

    function it_is_not_applicable_for_other_pieces()
    {
        $this->areApplicableFor(Knight::forColor(Color::white()))->shouldBe(false);
    }

    function it_may_move_to_any_square_along_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 4),
            CoordinatePair::fromFileAndRank('a', 2),
            new AlongFile()
        );

        $this->mayMove($this->rook, $move);
    }

    function it_may_move_to_any_square_along_rank()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('b', 1),
            CoordinatePair::fromFileAndRank('d', 1),
            new AlongRank()
        );

        $this->mayMove($this->rook, $move);
    }

    function it_may_not_move_to_adjoining_square_along_diagonal()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new AlongDiagonal()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->rook, $move))->during('mayMove', [$this->rook, $move,]);
    }

    function it_may_not_move_over_any_intervening_pieces()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 4),
            new AlongFile()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->rook, $move))->during('mayMove', [$this->rook, $move,]);
    }

    function it_may_move_by_castling()
    {
        $source = CoordinatePair::fromFileAndRank('f', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 1);
        $move = new Castling(
            Color::white(),
            $source,
            $destination
        );

        $this->mayMove($this->rook, $move);
    }

    function it_may_not_move_by_castling_when_this_rook_has_already_moved()
    {
        $source = CoordinatePair::fromFileAndRank('f', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 1);
        $move = new Castling(
            Color::white(),
            $source,
            $destination
        );

        $this->applyAfter(
            new PieceWasMoved(
                $this->rook,
                CoordinatePair::fromFileAndRank('a', 1),
                CoordinatePair::fromFileAndRank('a', 3)
            )
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->rook, $move))->during('mayMove', [$this->rook, $move,]);
    }

    function it_may_move_by_castling_if_another_rook_has_moved()
    {
        $source = CoordinatePair::fromFileAndRank('f', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 1);
        $move = new Castling(
            Color::white(),
            $source,
            $destination
        );

        $this->applyAfter(
            new PieceWasMoved(
                Rook::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('h', 1),
                CoordinatePair::fromFileAndRank('g', 1)
            )
        );

        $this->mayMove($this->rook, $move);
    }

    function it_may_move_by_castling_if_opponents_rook_has_moved()
    {
        $source = CoordinatePair::fromFileAndRank('f', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 1);
        $move = new Castling(
            Color::white(),
            $source,
            $destination
        );

        $this->applyAfter(
            new PieceWasMoved(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('h', 8),
                CoordinatePair::fromFileAndRank('g', 8)
            )
        );

        $this->mayMove($this->rook, $move);
    }
}
