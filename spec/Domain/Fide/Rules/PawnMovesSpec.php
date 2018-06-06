<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;
use PhpSpec\ObjectBehavior;

class PawnMovesSpec extends ObjectBehavior
{
    /**
     * @var Pawn
     */
    private $whitePawn;
    
    /**
     * @var Pawn
     */
    private $blackPawn;

    function let()
    {
        $this->whitePawn = Pawn::forColor(Color::white());
        $this->blackPawn = Pawn::forColor(Color::black());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PawnMoves::class);
    }

    function it_is_piece_moves_rule()
    {
        $this->shouldBeAnInstanceOf(PieceMoves::class);
    }

    function it_is_applicable_for_pawn()
    {
        $this->isApplicableFor($this->whitePawn)->shouldBe(true);
    }

    function it_is_not_applicable_for_other_pieces()
    {
        $this->isApplicableFor(Rook::forColor(Color::white()))->shouldBe(false);
    }

    function it_may_move_forward_to_the_square_immediately_in_front_on_the_same_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 3),
            new Forward(Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->mayMove($this->whitePawn, $move);
    }

    function it_may_not_move_backward_to_the_square_immediately_in_front_on_the_same_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->whitePawn, $move))->during('mayMove', [$this->whitePawn, $move,]);
    }

    function it_may_not_move_forward_for_another_color_to_the_square_immediately_in_front_on_the_same_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 7),
            CoordinatePair::fromFileAndRank('a', 8),
            new Forward(Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->blackPawn, $move))->during('mayMove', [$this->blackPawn, $move,]);
    }

    function it_may_not_move_along_diagonal()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 3),
            new Forward(Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal())
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->whitePawn, $move))->during('mayMove', [$this->whitePawn, $move,]);
    }

    function it_may_not_move_along_rank()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->whitePawn, $move))->during('mayMove', [$this->whitePawn, $move,]);
    }

    function it_may_not_capture_along_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 3),
            new Forward(Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->applyAfter(
            new PieceWasMoved(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('a', 4),
                CoordinatePair::fromFileAndRank('a', 3)
            )
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->whitePawn, $move))->during('mayMove', [$this->whitePawn, $move,]);
    }

    function it_may_capture_opponents_piece_diagonally_in_front_of_it_on_an_adjacent_file()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 5),
            CoordinatePair::fromFileAndRank('b', 4),
            new Forward(Color::black(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal())
        );

        $this->applyAfter(
            new PieceWasMoved(
                Rook::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('b', 5),
                CoordinatePair::fromFileAndRank('b', 4)
            )
        );

        $this->mayMove($this->blackPawn, $move);
    }

    function it_may_advance_two_squares_along_the_same_file_on_first_move_provided_both_are_unoccupied()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 7),
            CoordinatePair::fromFileAndRank('a', 5),
            new Forward(Color::black(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->mayMove($this->blackPawn, $move);
    }

    function it_may_not_move_more_than_to_the_square_immediately_in_front_on_the_same_file_on_next_moves()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 3),
            CoordinatePair::fromFileAndRank('a', 5),
            new Forward(Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->applyAfter(
            new PieceWasMoved(
                $this->whitePawn,
                CoordinatePair::fromFileAndRank('a', 2),
                CoordinatePair::fromFileAndRank('a', 3)
            )
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->whitePawn, $move))->during('mayMove', [$this->whitePawn, $move,]);
    }

    function it_may_not_advance_more_than_two_squares_along_the_same_file_on_first_move()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 6),
            new Forward(Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->whitePawn, $move))->during('mayMove', [$this->whitePawn, $move,]);
    }

    function it_may_not_capture_opponents_piece_diagonally_not_directly_in_front_of_it()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('c', 4),
            new Forward(Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal())
        );

        $this->applyAfter(
            new PieceWasMoved(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('c', 5),
                CoordinatePair::fromFileAndRank('c', 4)
            )
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->whitePawn, $move))->during('mayMove', [$this->whitePawn, $move,]);
    }

    function it_may_move_forward_if_destination_is_not_occupied_at_the_moment()
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 3),
            new Forward(Color::white(), new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile())
        );

        $blackRook = Rook::forColor(Color::black());
        $this->applyAfter(
            new PieceWasMoved(
                $blackRook,
                CoordinatePair::fromFileAndRank('b', 3),
                CoordinatePair::fromFileAndRank('a', 3)
            )
        );

        $this->applyAfter(
            new PieceWasMoved(
                $blackRook,
                CoordinatePair::fromFileAndRank('a', 3),
                CoordinatePair::fromFileAndRank('d', 3)
            )
        );

        $this->mayMove($this->whitePawn, $move);
    }
}
