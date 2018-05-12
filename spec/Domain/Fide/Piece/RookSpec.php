<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class RookSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Rook::class);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_rook_if_same_color()
    {
        $pawn = Rook::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_may_move_to_any_square_along_file(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 5),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
        );

        $this->mayMove($move, $board);
    }

    function it_may_move_to_any_square_along_rank(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('g', 2),
            CoordinatePair::fromFileAndRank('d', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
        );

        $this->mayMove($move, $board);
    }

    function it_may_not_move_to_any_square_along_diagonal(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('d', 6),
            CoordinatePair::fromFileAndRank('a', 3),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_to_square_not_on_same_file_or_rank_or_diagonal(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('d', 6),
            CoordinatePair::fromFileAndRank('c', 4),
            new LShaped()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_over_intervening_pieces(Board $board)
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('d', 6),
            CoordinatePair::fromFileAndRank('c', 4),
            new LShaped()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_move_by_castling(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('f', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 1);
        $move = new Castling(
            Piece\Color::white(),
            $source,
            $destination
        );

        $this->placeAt(CoordinatePair::fromFileAndRank('a', 1));
        $this->mayMove($move, $board);
    }

    function it_may_not_move_by_castling_when_has_already_moved(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('f', 1);
        $destination = CoordinatePair::fromFileAndRank('d', 1);
        $move = new Castling(
            Piece\Color::white(),
            $source,
            $destination
        );

        $this->placeAt(CoordinatePair::fromFileAndRank('a', 1));
        $this->placeAt(CoordinatePair::fromFileAndRank('b', 1));
        $this->placeAt(CoordinatePair::fromFileAndRank('a', 1));

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_intents_not_intervened_move_to_any_square()
    {
        $source = CoordinatePair::fromFileAndRank('d', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 2);

        $this->placeAt($source);
        $this->intentMoveTo($destination)->shouldBeLike(
            new NotIntervened(
                $source,
                $destination,
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
            )
        );
    }

    function it_may_not_intent_move_to_illegal_position()
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('b', 2);

        $this->placeAt($source);

        $this->shouldThrow(new MoveToIllegalPosition($this->getWrappedObject(), $source, $destination))->during('intentMoveTo', [$destination,]);
    }
}
