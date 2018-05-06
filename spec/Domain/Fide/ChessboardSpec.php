<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Exception\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\OutOfBoardPosition;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Fide\Move\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Square;
use NicholasZyl\Chess\Domain\Fide\Square\CoordinatePair;
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

    function it_is_composed_of_sixty_four_squares()
    {
        $this->beConstructedWith(
            [Square::forCoordinates(CoordinatePair::fromFileAndRank('a', 1)),]
        );

        $this->shouldThrow(new \InvalidArgumentException('The chessboard must be composed of an 8 x 8 grid of 64 equal squares.'))->duringInstantiation();
    }

    function it_does_not_allow_interacting_with_position_out_of_board(Coordinates $coordinates)
    {
        $coordinates->__toString()->willReturn('i1');

        $this->shouldThrow(new OutOfBoardPosition($coordinates->getWrappedObject()))->during('verifyThatPositionIsUnoccupied', [$coordinates,]);
    }

    function it_allows_placing_piece_at_given_coordinates()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());
        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAtCoordinates($whitePawn, $coordinates);
    }

    function it_allows_moving_white_pawn_one_square_forward()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);

        $this->placePieceAtCoordinates($whitePawn, $source);

        $this->movePiece(AlongFile::between($source, $destination));

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

        $this->shouldThrow(new SquareIsOccupied($position))->during('verifyThatPositionIsUnoccupied', [$position,]);
    }

    function it_does_not_allow_illegal_move_for_piece()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $from = CoordinatePair::fromFileAndRank('b', 2);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $move = AlongFile::between($from, $to);

        $illegalMove = new MoveNotAllowedForPiece($move, $whitePawn);
        $this->placePieceAtCoordinates($whitePawn, $from);

        $this->shouldThrow($illegalMove)->during('movePiece', [$move,]);

        $this->hasPieceAtCoordinates($whitePawn, $from)->shouldBe(true);
        $this->hasPieceAtCoordinates($whitePawn, $to)->shouldBe(false);
    }
}
