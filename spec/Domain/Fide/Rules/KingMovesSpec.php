<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event\PieceWasPlacedAt;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Rules\KingMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;
use PhpSpec\ObjectBehavior;

class KingMovesSpec extends ObjectBehavior
{
    /**
     * @var King
     */
    private $king;

    function let()
    {
        $this->king = King::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(KingMoves::class);
    }

    function it_is_piece_moves_rule()
    {
        $this->shouldBeAnInstanceOf(PieceMoves::class);
    }

    function it_is_applicable_for_king_piece()
    {
        $this->areApplicableFor($this->king)->shouldBe(true);
    }

    function it_is_not_applicable_for_other_pieces()
    {
        $this->areApplicableFor(Knight::forColor(Color::white()))->shouldBe(false);
    }

    function it_verifies_as_valid_move_to_adjoining_square_along_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2),
            new AlongFile()
        );

        $this->verify($this->king, $move);
    }

    function it_verifies_as_valid_move_to_adjoining_square_along_rank()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 1),
            new AlongRank()
        );

        $this->verify($this->king, $move);
    }

    function it_verifies_as_valid_move_to_adjoining_square_along_diagonal()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new AlongDiagonal()
        );

        $this->verify($this->king, $move);
    }

    function it_verifies_as_invalid_move_over_any_intervening_pieces()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new AlongDiagonal()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->king, $move))->during('verify', [$this->king, $move,]);
    }

    function it_verifies_as_invalid_move_further_than_to_adjoining_square()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3),
            new AlongFile()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->king, $move))->during('verify', [$this->king, $move,]);
    }

    function it_verifies_as_valid_move_by_castling()
    {
        $source = CoordinatePair::fromFileAndRank('f', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 1);
        $move = new Castling(
            Color::white(),
            $source,
            $destination
        );

        $this->verify($this->king, $move);
    }

    function it_verifies_as_invalid_move_by_castling_when_king_has_already_moved()
    {
        $source = CoordinatePair::fromFileAndRank('f', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 1);
        $move = new Castling(
            Color::white(),
            $source,
            $destination
        );

        $this->applyAfter(
            new PieceWasPlacedAt(
                $this->king,
                $source
            )
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->king, $move))->during('verify', [$this->king, $move,]);
    }

    function it_verifies_as_valid_move_by_castling_if_opponents_king_has_moved()
    {
        $source = CoordinatePair::fromFileAndRank('f', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 1);
        $move = new Castling(
            Color::white(),
            $source,
            $destination
        );

        $this->applyAfter(
            new PieceWasPlacedAt(
                King::forColor(Color::black()),
                $source
            )
        );

        $this->verify($this->king, $move);
    }
}
