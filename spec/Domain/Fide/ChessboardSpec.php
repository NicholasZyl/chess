<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoardCoordinates;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\Move\ToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Square;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class ChessboardSpec extends ObjectBehavior
{
    function let()
    {
        $grid = [];
        foreach (CoordinatePair::validFiles() as $file) {
            foreach (CoordinatePair::validRanks() as $rank) {
                $coordinates = CoordinatePair::fromFileAndRank($file, $rank);
                $grid[] = Square::forCoordinates($coordinates);
            }
        }
        $this->beConstructedWith($grid);
    }

    function it_must_be_composed_of_sixty_four_squares()
    {
        $this->beConstructedWith(
            [Square::forCoordinates(CoordinatePair::fromFileAndRank('a', 1)),]
        );

        $this->shouldThrow(new \InvalidArgumentException('The chessboard must be composed of an 8 x 8 grid of 64 equal squares.'))->duringInstantiation();
    }

    function it_does_not_allow_interacting_with_position_out_of_board(Coordinates $coordinates)
    {
        $coordinates->__toString()->willReturn('i1');

        $this->shouldThrow(new OutOfBoardCoordinates($coordinates->getWrappedObject()))->during('verifyThatPositionIsUnoccupied', [$coordinates,]);
    }

    function it_allows_placing_piece_at_given_coordinates()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());
        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAtCoordinates($whitePawn, $coordinates);
    }

    function it_allows_moving_piece_if_move_is_legal()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);

        $this->placePieceAtCoordinates($whitePawn, $source);

        $this->movePiece($source, $destination);

        $this->hasPieceAtCoordinates($whitePawn, $source)->shouldBe(false);
        $this->hasPieceAtCoordinates($whitePawn, $destination)->shouldBe(true);
    }

    function it_knows_what_piece_is_placed_on_square_at_given_coordinates()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $coordinates = CoordinatePair::fromFileAndRank('b', 2);
        $this->placePieceAtCoordinates($whitePawn, $coordinates);

        $this->hasPieceAtCoordinates($whitePawn, $coordinates)->shouldBe(true);
    }

    function it_knows_when_square_at_given_coordinates_is_unoccupied()
    {
        $this->verifyThatPositionIsUnoccupied(CoordinatePair::fromFileAndRank('a', 1));
    }

    function it_knows_when_square_at_given_coordinates_is_occupied()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);
        $this->placePieceAtCoordinates(
            Pawn::forColor(Piece\Color::white()),
            $position
        );

        $this->shouldThrow(SquareIsOccupied::class)->during('verifyThatPositionIsUnoccupied', [$position,]);
    }

    function it_allows_picking_up_piece_from_coordinates()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);
        $pawn = Pawn::forColor(Piece\Color::white());
        $this->placePieceAtCoordinates($pawn, $position);

        $this->pickPieceFromCoordinates($position)->shouldBe($pawn);
        $this->hasPieceAtCoordinates($pawn, $position)->shouldBe(false);
    }

    function it_fails_if_trying_to_pick_piece_from_unoccupied_position()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);

        $this->shouldThrow(new SquareIsUnoccupied($position))->during('pickPieceFromCoordinates', [$position,]);
    }

    function it_does_not_allow_illegal_move_for_piece()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 1);

        $illegalMove = new ToIllegalPosition($whitePawn, $source, $destination);
        $this->placePieceAtCoordinates($whitePawn, $source);

        $this->shouldThrow($illegalMove)->during('movePiece', [$source, $destination,]);

        $this->hasPieceAtCoordinates($whitePawn, $source)->shouldBe(true);
        $this->hasPieceAtCoordinates($whitePawn, $destination)->shouldBe(false);
    }

    function it_allows_capturing_opponents_piece()
    {
        $whiteRook = Rook::forColor(Piece\Color::white());
        $blackPawn = Pawn::forColor(Piece\Color::black());

        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);

        $this->placePieceAtCoordinates($whiteRook, $source);
        $this->placePieceAtCoordinates($blackPawn, $destination);

        $this->movePiece($source, $destination);

        $this->hasPieceAtCoordinates($blackPawn, $destination)->shouldBe(false);
        $this->hasPieceAtCoordinates($whiteRook, $destination)->shouldBe(true);
    }

    function it_does_not_allow_move_to_square_occupied_by_piece_of_same_color()
    {
        $whiteRook = Rook::forColor(Piece\Color::white());
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);

        $this->placePieceAtCoordinates($whiteRook, $source);
        $this->placePieceAtCoordinates($whitePawn, $destination);

        $this->shouldThrow(new MoveToOccupiedPosition($destination))->during('movePiece', [$source, $destination,]);
        $this->hasPieceAtCoordinates($whiteRook, $source)->shouldBe(true);
    }

    function it_knows_when_square_at_coordinates_is_occupied_by_opponent_if_piece_has_different_color()
    {
        $blackPawn = Pawn::forColor(Piece\Color::black());
        $position = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAtCoordinates($blackPawn, $position);

        $this->hasOpponentsPieceAt($position, Piece\Color::white())->shouldBe(true);
    }

    function it_knows_when_square_at_coordinates_is_not_occupied_by_opponent_if_piece_has_same_color()
    {
        $blackPawn = Pawn::forColor(Piece\Color::black());
        $position = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAtCoordinates($blackPawn, $position);

        $this->hasOpponentsPieceAt($position, Piece\Color::black())->shouldBe(false);
    }

    function it_knows_when_square_at_coordinates_is_not_occupied_by_opponent_if_piece_is_unoccupied()
    {
        $position = CoordinatePair::fromFileAndRank('c', 4);

        $this->hasOpponentsPieceAt($position, Piece\Color::black())->shouldBe(false);
    }
}
