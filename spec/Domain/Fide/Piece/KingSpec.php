<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Move\Castling;
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

    function it_may_move_to_adjoining_square_along_file(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
        );

        $this->mayMove($move, $board);
    }

    function it_may_move_to_adjoining_square_along_rank(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
        );

        $this->mayMove($move, $board);
    }

    function it_may_move_to_adjoining_square_along_diagonal(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->mayMove($move, $board);
    }

    function it_may_not_move_over_any_intervening_pieces(Board $board)
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_to_nearest_square(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('c', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_more_than_to_adjoining_square(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_capture_at_any_square_along_a_diagonal_on_which_it_stands(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->mayMove($move, $board);
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

        $this->placeAt($source);
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

        $this->placeAt($source);
        $this->placeAt(CoordinatePair::fromFileAndRank('g', 1));
        $this->placeAt($source);

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
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

        $this->shouldThrow(new MoveToIllegalPosition($this->getWrappedObject(), $source, $destination))->during('intentMoveTo', [$destination,]);
    }

    function it_may_intent_castling_move()
    {
        $source = CoordinatePair::fromFileAndRank('e', 1);
        $destination = CoordinatePair::fromFileAndRank('c', 1);

        $this->placeAt($source);
        $this->intentMoveTo($destination)->shouldBeLike(
            new Castling(
                Piece\Color::white(),
                $source,
                $destination
            )
        );
    }

    function it_may_not_intent_castling_if_has_already_moved()
    {
        $source = CoordinatePair::fromFileAndRank('e', 1);
        $destination = CoordinatePair::fromFileAndRank('g', 1);

        $this->placeAt($source);
        $this->placeAt(CoordinatePair::fromFileAndRank('g', 1));
        $this->placeAt($source);

        $this->shouldThrow(new MoveToIllegalPosition($this->getWrappedObject(), $source, $destination))->during('intentMoveTo', [$destination,]);
    }

    function it_is_attacking_along_valid_move(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('a', 2);

        $this->placeAt($source);

        $this->isAttacking($destination, $board)->shouldBe(true);
    }

    function it_is_not_attacking_if_move_is_illegal_for_piece(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('a', 3);

        $this->placeAt($source);

        $this->isAttacking($destination, $board)->shouldBe(false);
    }
}
