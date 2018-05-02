<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\Coordinates;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class ChessboardSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Chessboard::class);
    }

    function it_allows_placing_piece_at_given_coordinates()
    {
        $piece = Pawn::forColor(Piece\Color::white());
        $coordinates = Coordinates::fromFileAndRank('b', 2);

        $this->placePieceAtCoordinates($piece, $coordinates);
    }

    function it_allows_moving_piece_from_one_coordinate_to_another()
    {
        $piece = Pawn::forColor(Piece\Color::white());

        $source = Coordinates::fromFileAndRank('b', 2);
        $destination = Coordinates::fromFileAndRank('b', 3);

        $this->placePieceAtCoordinates($piece, $source);

        $this->movePiece($source, $destination);

        $this->hasPieceAtCoordinates($piece, $source)->shouldBe(false);
        $this->hasPieceAtCoordinates($piece, $destination)->shouldBe(true);
    }

    function it_knows_what_piece_is_placed_on_square_at_given_coordinates()
    {
        $piece = Pawn::forColor(Piece\Color::white());

        $coordinates = Coordinates::fromFileAndRank('b', 2);
        $this->placePieceAtCoordinates($piece, $coordinates);

        $this->hasPieceAtCoordinates($piece, $coordinates)->shouldBe(true);
    }

    function it_does_not_allow_move_that_is_illegal(Piece $piece)
    {
        $from = Coordinates::fromFileAndRank('b', 2);
        $to = Coordinates::fromFileAndRank('c', 2);

        $illegalMove = new IllegalMove($from, $to);
        $piece->intentMove($from, $to)->willThrow($illegalMove);
        $piece->isSameAs($piece)->willReturn(true);
        $this->placePieceAtCoordinates($piece, $from);

        $this->shouldThrow($illegalMove)->during('movePiece', [$from, $to,]);

        $this->hasPieceAtCoordinates($piece, $from)->shouldBe(true);
        $this->hasPieceAtCoordinates($piece, $to)->shouldBe(false);
    }

    function it_does_not_move_piece_to_square_with_another_piece_with_same_color_placed_on_it()
    {
        $piece = Queen::forColor(Piece\Color::white());
        $anotherPiece = Knight::forColor(Piece\Color::white());

        $source = Coordinates::fromFileAndRank('b', 2);
        $destination = Coordinates::fromFileAndRank('c', 2);

        $this->placePieceAtCoordinates($piece, $source);
        $this->placePieceAtCoordinates($anotherPiece, $destination);

        $this->shouldThrow(new Chessboard\Exception\SquareIsOccupied($destination))->during('movePiece', [$source, $destination,]);

        $this->hasPieceAtCoordinates($piece, $source)->shouldBe(true);
        $this->hasPieceAtCoordinates($anotherPiece, $destination)->shouldBe(true);
    }
}
