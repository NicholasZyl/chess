<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Rules\BishopMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;
use PhpSpec\ObjectBehavior;

class BishopMovesSpec extends ObjectBehavior
{
    /**
     * @var Bishop
     */
    private $bishop;

    function let()
    {
        $this->bishop = Bishop::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BishopMoves::class);
    }

    function it_is_piece_moves_rule()
    {
        $this->shouldBeAnInstanceOf(PieceMoves::class);
    }

    function it_is_applicable_for_bishop()
    {
        $this->areApplicableFor($this->bishop)->shouldBe(true);
    }

    function it_is_not_applicable_for_other_pieces()
    {
        $this->areApplicableFor(Knight::forColor(Color::white()))->shouldBe(false);
    }

    function it_may_move_to_any_square_along_a_diagonal()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3),
            new AlongDiagonal()
        );

        $this->mayMove($this->bishop, $move);
    }

    function it_may_not_move_along_rank()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 1),
            new AlongRank()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->bishop, $move))->during('mayMove', [$this->bishop, $move,]);
    }

    function it_may_not_move_along_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('c', 1),
            CoordinatePair::fromFileAndRank('c', 2),
            new AlongFile()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->bishop, $move))->during('mayMove', [$this->bishop, $move,]);
    }

    function it_may_not_move_over_any_intervening_pieces()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new AlongDiagonal()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->bishop, $move))->during('mayMove', [$this->bishop, $move,]);
    }
}
