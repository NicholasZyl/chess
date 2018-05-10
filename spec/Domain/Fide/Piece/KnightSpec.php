<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Move\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class KnightSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Knight::class);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_knight_if_same_color()
    {
        $pawn = Knight::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_may_move_to_one_of_the_squares_nearest_to_that_on_which_it_stands_but_not_on_same_rank_file_or_diagonal()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 3),
            new LShaped()
        );

        $this->mayMove($move);
    }

    function it_may_not_move_to_adjoining_square()
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move,]);
    }

    function it_may_not_move_along_file()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 5),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move,]);
    }

    function it_may_not_move_along_rank()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 5),
            CoordinatePair::fromFileAndRank('f', 5),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move,]);
    }

    function it_may_not_move_along_diagonal()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('h', 8),
            CoordinatePair::fromFileAndRank('a', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move,]);
    }

    function it_may_not_be_blocked_by_intervening_pieces()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('h', 8),
            CoordinatePair::fromFileAndRank('a', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move,]);
    }

    function it_may_capture_the_same_way_it_moves()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 3),
            new LShaped()
        );

        $this->mayMove($move);
    }

    function it_intents_move_to_adjoining_square()
    {
        $source = CoordinatePair::fromFileAndRank('d', 3);
        $destination = CoordinatePair::fromFileAndRank('e', 1);

        $this->placeAt($source);
        $this->intentMoveTo($destination)->shouldBeLike(
            new OverOtherPieces(
                $source,
                $destination,
                new LShaped()
            )
        );
    }

    function it_may_not_intent_move_to_illegal_position()
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('a', 3);

        $this->placeAt($source);

        $this->shouldThrow(new ToIllegalPosition($this->getWrappedObject(), $source, $destination))->during('intentMoveTo', [$destination,]);
    }
}
