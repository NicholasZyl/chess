<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Fide\Rules\KnightMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;
use PhpSpec\ObjectBehavior;

class KnightMovesSpec extends ObjectBehavior
{
    /**
     * @var Knight
     */
    private $knight;

    function let()
    {
        $this->knight = Knight::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(KnightMoves::class);
    }

    function it_is_piece_moves_rule()
    {
        $this->shouldBeAnInstanceOf(PieceMoves::class);
    }

    function it_is_applicable_for_knight()
    {
        $this->areApplicableFor($this->knight)->shouldBe(true);
    }

    function it_is_not_applicable_for_other_pieces()
    {
        $this->areApplicableFor(Queen::forColor(Color::white()))->shouldBe(false);
    }

    function it_may_move_to_the_nearest_square_over_other_pieces()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 3),
            new LShaped()
        );

        $this->mayMove($this->knight, $move);
    }

    function it_may_not_move_along_rank()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 1),
            new AlongRank()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->knight, $move))->during('mayMove', [$this->knight, $move,]);
    }

    function it_may_not_move_along_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('c', 1),
            CoordinatePair::fromFileAndRank('c', 2),
            new AlongFile()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->knight, $move))->during('mayMove', [$this->knight, $move,]);
    }
}
