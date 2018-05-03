<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongFile;
use NicholasZyl\Chess\Domain\Chessboard\Move\AlongRank;
use NicholasZyl\Chess\Domain\Chessboard\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class KingSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(King::class);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_king_if_same_color()
    {
        $pawn = King::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_can_move_along_file()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 2);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->mayMove($move);
    }

    function it_can_move_along_rank()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $move = AlongRank::between(
            $from,
            $to
        );

        $this->mayMove($move);
    }

    function it_can_move_along_diagonal()
    {
        $move = AlongDiagonal::between(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->mayMove($move);
    }

    function it_cannot_move_to_nearest_square()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('c', 2);
        $move = NearestNotSameFileRankOrDiagonal::between(
            $from,
            $to
        );

        $this->shouldThrow(IllegalMove::forMove($move))->during('mayMove', [$move,]);
    }

    function it_cannot_move_more_than_one_square()
    {
        $from = CoordinatePair::fromFileAndRank('a', 1);
        $to = CoordinatePair::fromFileAndRank('a', 3);
        $move = AlongFile::between(
            $from,
            $to
        );

        $this->shouldThrow(IllegalMove::forMove($move))->during('mayMove', [$move,]);
    }

    function it_can_move_to_adjoining_square_forward()
    {
        $from = CoordinatePair::fromString('c3');
        $to = CoordinatePair::fromString('c4');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_adjoining_square_backward()
    {
        $from = CoordinatePair::fromString('c3');
        $to = CoordinatePair::fromString('c2');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_adjoining_square_queenside()
    {
        $from = CoordinatePair::fromString('c3');
        $to = CoordinatePair::fromString('b3');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_adjoining_square_kingside()
    {
        $from = CoordinatePair::fromString('c3');
        $to = CoordinatePair::fromString('d3');

        $this->intentMove($from, $to);
    }

    function it_can_move_to_adjoining_square_along_diagonal()
    {
        $from = CoordinatePair::fromString('c3');
        $to = CoordinatePair::fromString('d4');

        $this->intentMove($from, $to);
    }

    function it_can_not_move_more_than_one_square()
    {
        $from = CoordinatePair::fromString('c3');
        $to = CoordinatePair::fromString('c5');

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }

    function it_cannot_move_to_other_square()
    {
        $from = CoordinatePair::fromString('d4');
        $to = CoordinatePair::fromString('c2');

        $this->shouldThrow(new IllegalMove($from, $to))->during('intentMove', [$from, $to,]);
    }
}
