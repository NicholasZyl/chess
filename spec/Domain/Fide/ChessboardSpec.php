<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasPlaced;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoardCoordinates;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class ChessboardSpec extends ObjectBehavior
{
    function it_is_composed_of_sixty_four_squares()
    {
        $coordinates = CoordinatePair::fromFileAndRank('h', 8);
        $this->isPositionOccupied($coordinates);
    }

    function it_does_not_allow_interacting_with_position_out_of_board()
    {
        $coordinates = CoordinatePair::fromFileAndRank('i', 9);

        $this->shouldThrow(new OutOfBoardCoordinates($coordinates))->during('isPositionOccupied', [$coordinates,]);
    }

    function it_allows_placing_piece_at_given_coordinates()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());
        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAtCoordinates($whitePawn, $coordinates)->shouldBeLike([new PieceWasPlaced($whitePawn, $coordinates),]);
    }

    function it_knows_when_opponents_piece_was_captured()
    {
        $whiteRook = Rook::forColor(Piece\Color::white());
        $blackPawn = Pawn::forColor(Piece\Color::black());

        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAtCoordinates($blackPawn, $coordinates);
        $this->placePieceAtCoordinates($whiteRook, $coordinates)->shouldBeLike(
            [new PieceWasPlaced($whiteRook, $coordinates), new PieceWasCaptured($blackPawn, $coordinates),]
        );
    }

    function it_does_not_allow_placing_piece_on_coordinates_occupied_by_same_color()
    {
        $whiteRook = Rook::forColor(Piece\Color::white());
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAtCoordinates($whiteRook, $coordinates);

        $this->shouldThrow(SquareIsOccupied::class)->during('placePieceAtCoordinates', [$whitePawn, $coordinates,]);
    }

    function it_allows_picking_up_piece_from_coordinates()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);
        $pawn = Pawn::forColor(Piece\Color::white());
        $this->placePieceAtCoordinates($pawn, $position);

        $this->pickPieceFromCoordinates($position)->shouldBe($pawn);
    }

    function it_does_not_allow_picking_a_piece_from_unoccupied_position()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);

        $this->shouldThrow(new SquareIsUnoccupied($position))->during('pickPieceFromCoordinates', [$position,]);
    }

    function it_knows_when_square_at_given_coordinates_is_unoccupied()
    {
        $this->isPositionOccupied(CoordinatePair::fromFileAndRank('a', 1))->shouldBe(false);
    }

    function it_knows_when_square_at_given_coordinates_is_occupied()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);
        $this->placePieceAtCoordinates(
            Pawn::forColor(Piece\Color::white()),
            $position
        );

        $this->isPositionOccupied($position)->shouldBe(true);
    }

    function it_knows_when_square_at_coordinates_is_occupied_by_opponent_if_piece_has_different_color()
    {
        $blackPawn = Pawn::forColor(Piece\Color::black());
        $position = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAtCoordinates($blackPawn, $position);

        $this->isPositionOccupiedByOpponentOf($position, Piece\Color::white())->shouldBe(true);
    }

    function it_knows_when_square_at_coordinates_is_not_occupied_by_opponent_if_piece_has_same_color()
    {
        $blackPawn = Pawn::forColor(Piece\Color::black());
        $position = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAtCoordinates($blackPawn, $position);

        $this->isPositionOccupiedByOpponentOf($position, Piece\Color::black())->shouldBe(false);
    }

    function it_knows_when_square_at_coordinates_is_not_occupied_by_opponent_if_piece_is_unoccupied()
    {
        $position = CoordinatePair::fromFileAndRank('c', 4);

        $this->isPositionOccupiedByOpponentOf($position, Piece\Color::black())->shouldBe(false);
    }

    function it_knows_when_square_is_attacked_by_given_color(Game $game)
    {
        $position = CoordinatePair::fromFileAndRank('b', 3);
        $opponentsPiece = Pawn::forColor(Piece\Color::black());
        $opponentsPiecePosition = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAtCoordinates($opponentsPiece, $opponentsPiecePosition);
        $this->placePieceAtCoordinates(Pawn::forColor(Piece\Color::white()), $position);

        $game->mayMove($opponentsPiece, $opponentsPiecePosition, $position)->shouldBeCalled()->willReturn(true);

        $this->isPositionAttackedBy($position, Piece\Color::black(), $game)->shouldBe(true);
    }

    function it_knows_when_square_is_not_attacked_by_opponents_piece(Game $game)
    {
        $blackPawn = Pawn::forColor(Piece\Color::black());
        $blackPawnPosition = CoordinatePair::fromFileAndRank('a', 3);
        $this->placePieceAtCoordinates($blackPawn, $blackPawnPosition);
        $blackRook = Rook::forColor(Piece\Color::black());
        $blackRookPosition = CoordinatePair::fromFileAndRank('a', 8);
        $this->placePieceAtCoordinates($blackRook, $blackRookPosition);
        $this->placePieceAtCoordinates(Bishop::forColor(Piece\Color::white()), CoordinatePair::fromFileAndRank('b', 3));

        $position = CoordinatePair::fromFileAndRank('a', 2);

        $game->mayMove($blackPawn, $blackPawnPosition, $position)->shouldBeCalled()->willReturn(false);
        $game->mayMove($blackRook, $blackRookPosition, $position)->shouldBeCalled()->willReturn(false);

        $this->isPositionAttackedBy($position, Piece\Color::black(), $game)->shouldBe(false);
    }

    function it_cannot_check_if_square_out_of_board_is_attacked(Game $game)
    {
        $coordinates = CoordinatePair::fromFileAndRank('z', 9);

        $this->shouldThrow(new OutOfBoardCoordinates($coordinates))->during('isPositionAttackedBy', [$coordinates, Piece\Color::white(), $game,]);
    }

    function it_removes_piece_from_given_position()
    {
        $position = CoordinatePair::fromFileAndRank('b', 2);
        $piece = Pawn::forColor(Piece\Color::white());
        $this->placePieceAtCoordinates($piece, $position);

        $this->removePieceFrom($position)->shouldBe($piece);

        $this->isPositionOccupied($position)->shouldBe(false);
    }

    function it_cannot_remove_piece_from_unoccupied_square()
    {
        $position = CoordinatePair::fromFileAndRank('b', 2);

        $this->shouldThrow(new SquareIsUnoccupied($position))->during('removePieceFrom', [$position,]);
    }
}
