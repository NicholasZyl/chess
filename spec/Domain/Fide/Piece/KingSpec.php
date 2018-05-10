<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Move\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Move\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Move\NearestNotSameFileRankOrDiagonal;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
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

    function it_may_move_to_adjoining_square_along_file()
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
        );

        $this->canMove($move);
    }

    function it_may_move_to_adjoining_square_along_rank()
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
        );

        $this->canMove($move);
    }

    function it_may_move_to_adjoining_square_along_diagonal()
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->canMove($move);
    }

    function it_may_not_move_over_any_intervening_pieces()
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_not_move_to_nearest_square()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('c', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped()
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_not_move_more_than_to_adjoining_square()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
        );

        $this->shouldThrow(new NotAllowedForPiece($this->getWrappedObject(), $move))->during('canMove', [$move,]);
    }

    function it_may_capture_at_any_square_along_a_diagonal_on_which_it_stands()
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->canMove($move);
    }

    function it_intents_move_to_adjoining_square()
    {
        $source = CoordinatePair::fromFileAndRank('d', 3);
        $destination = CoordinatePair::fromFileAndRank('d', 2);

        $this->placeAt($source);
        $this->intentMoveTo($destination)->shouldBeLike(
            new ToAdjoiningSquare(
                $source,
                $destination,
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
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
