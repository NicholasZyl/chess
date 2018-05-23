<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Move\AdvancingTwoSquares;
use NicholasZyl\Chess\Domain\Fide\Move\Capturing;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\ToAdjoiningSquare;
use NicholasZyl\Chess\Domain\Fide\Move\ToUnoccupiedSquare;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PawnSpec extends ObjectBehavior
{
    function let(Board $board)
    {
        $board->hasOpponentsPieceAt(Argument::cetera())->willReturn(false);
        $board->verifyThatPositionIsUnoccupied(Argument::cetera())->willReturn();

        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_has_color()
    {
        $this->color()->shouldBeLike(Piece\Color::white());
    }

    function it_is_same_as_another_pawn_if_same_color()
    {
        $pawn = Pawn::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_is_not_same_as_another_pawn_if_different_color()
    {
        $pawn = Pawn::forColor(Piece\Color::black());

        $this->isSameAs($pawn)->shouldBe(false);
    }

    function it_is_not_same_as_another_piece_even_if_same_color(Piece $piece)
    {
        $piece->color()->willReturn(Piece\Color::white());

        $this->isSameAs($piece)->shouldBe(false);
    }

    function it_may_move_forward_to_the_square_immediately_in_front_on_the_same_file(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2),
            new Forward(Piece\Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->mayMove($move, $board);
    }

    function it_may_not_move_backward_to_the_square_immediately_in_front_on_the_same_file(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_forward_for_another_color_to_the_square_immediately_in_front_on_the_same_file(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 1),
            new Forward(Piece\Color::black(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_along_diagonal(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new Forward(Piece\Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal())
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_along_rank(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_capture_along_file(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2),
            new Forward(Piece\Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );
        $board->verifyThatPositionIsUnoccupied(CoordinatePair::fromFileAndRank('a', 2))->willThrow(new SquareIsOccupied(CoordinatePair::fromFileAndRank('a', 2)));

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_capture_opponents_piece_diagonally_in_front_of_it_on_an_adjacent_file(Board $board)
    {
        $move = new ToAdjoiningSquare(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new Forward(Piece\Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal())
        );

        $board->hasOpponentsPieceAt(CoordinatePair::fromFileAndRank('b', 2), Color::white())->willReturn(true);

        $this->mayMove($move, $board);
    }

    function it_may_advance_two_squares_along_the_same_file_on_first_move_provided_both_are_unoccupied(Board $board)
    {
        $this->beConstructedThrough('forColor', [Piece\Color::black(),]);

        $move = new AdvancingTwoSquares(
            CoordinatePair::fromFileAndRank('a', 7),
            CoordinatePair::fromFileAndRank('a', 5),
            new Forward(Piece\Color::black(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->mayMove($move, $board);
    }

    function it_may_not_move_more_than_to_the_square_immediately_in_front_on_the_same_file_on_next_moves(Board $board)
    {
        $this->placeAt(CoordinatePair::fromFileAndRank('a', 2));
        $this->placeAt(CoordinatePair::fromFileAndRank('a', 3));

        $move = new AdvancingTwoSquares(
            CoordinatePair::fromFileAndRank('a', 3),
            CoordinatePair::fromFileAndRank('a', 5),
            new Forward(Piece\Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_advance_more_than_two_squares_along_the_same_file_on_first_move_provided_both_are_unoccupied(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 6),
            new Forward(Piece\Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_capture_opponents_piece_diagonally_not_directly_in_front_of_it(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3),
            new Forward(Piece\Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal())
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_intents_move_to_unoccupied_square_along_file()
    {
        $source = CoordinatePair::fromFileAndRank('d', 2);
        $destination = CoordinatePair::fromFileAndRank('d', 4);

        $this->placeAt($source);
        $this->intentMoveTo($destination)->shouldBeLike(
            new AdvancingTwoSquares(
                $source,
                $destination,
                new Forward(Piece\Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
            )
        );
    }

    function it_intents_capturing_move_along_diagonal()
    {
        $source = CoordinatePair::fromFileAndRank('d', 2);
        $destination = CoordinatePair::fromFileAndRank('e', 3);

        $this->placeAt($source);
        $this->intentMoveTo($destination)->shouldBeLike(
            new ToAdjoiningSquare(
                $source,
                $destination,
                new Forward(Piece\Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal())
            )
        );
    }

    function it_may_not_intent_move_to_illegal_position()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 6);

        $this->placeAt($source);

        $this->shouldThrow(new MoveToIllegalPosition($this->getWrappedObject(), $source, $destination))->during('intentMoveTo', [$destination,]);
    }

    function it_is_attacking_along_diagonal(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);

        $this->placeAt($source);

        $board->hasOpponentsPieceAt($destination, Color::white())->willReturn(true);

        $this->isAttacking($destination, $board)->shouldBe(true);
    }

    function it_is_not_attacking_if_move_is_illegal_for_piece(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);

        $this->placeAt($source);

        $this->isAttacking($destination, $board)->shouldBe(false);
    }
}
