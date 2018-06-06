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
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Fide\Rules\QueenMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;
use PhpSpec\ObjectBehavior;

class QueenMovesSpec extends ObjectBehavior
{
    /**
     * @var Queen
     */
    private $queen;

    function let()
    {
        $this->queen = Queen::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(QueenMoves::class);
    }

    function it_is_piece_moves_rule()
    {
        $this->shouldBeAnInstanceOf(PieceMoves::class);
    }

    function it_is_applicable_for_queen()
    {
        $this->isApplicableFor($this->queen)->shouldBe(true);
    }

    function it_is_not_applicable_for_other_pieces()
    {
        $this->isApplicableFor(Knight::forColor(Color::white()))->shouldBe(false);
    }

    function it_may_move_to_any_square_along_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2),
            new AlongFile()
        );

        $this->mayMove($this->queen, $move);
    }

    function it_may_move_to_any_square_along_rank()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('g', 1),
            CoordinatePair::fromFileAndRank('b', 1),
            new AlongRank()
        );

        $this->mayMove($this->queen, $move);
    }

    function it_may_move_to_any_square_along_diagonal()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('b', 1),
            new AlongDiagonal()
        );

        $this->mayMove($this->queen, $move);
    }

    function it_may_not_move_over_any_intervening_pieces()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new AlongDiagonal()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->queen, $move))->during('mayMove', [$this->queen, $move,]);
    }
}
