<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Chessboard;
use NicholasZyl\Chess\Domain\Chessboard\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
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

        $this->movePiece(Chessboard\Move\AlongFile::between($source, $destination));

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

    function it_does_not_illegal_move_for_piece()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $from = CoordinatePair::fromFileAndRank('b', 2);
        $to = CoordinatePair::fromFileAndRank('b', 1);
        $move = Chessboard\Move\AlongFile::between($from, $to);

        $illegalMove = IllegalMove::forMove($move);
        $this->placePieceAtCoordinates($whitePawn, $from);

        $this->shouldThrow($illegalMove)->during('movePiece', [$move,]);

        $this->hasPieceAtCoordinates($whitePawn, $from)->shouldBe(true);
        $this->hasPieceAtCoordinates($whitePawn, $to)->shouldBe(false);
    }

    function it_does_not_move_piece_to_square_with_another_piece_with_same_color_placed_on_it()
    {
        $whiteQueen = Queen::forColor(Piece\Color::white());
        $whiteKnight = Knight::forColor(Piece\Color::white());

        $whiteQueenPosition = CoordinatePair::fromFileAndRank('b', 2);
        $whiteKnightPosition = CoordinatePair::fromFileAndRank('c', 2);
        $move = Chessboard\Move\AlongRank::between($whiteQueenPosition, $whiteKnightPosition);

        $this->placePieceAtCoordinates($whiteQueen, $whiteQueenPosition);
        $this->placePieceAtCoordinates($whiteKnight, $whiteKnightPosition);

        $this->shouldThrow(new Chessboard\Exception\SquareIsOccupied($whiteKnightPosition))->during('movePiece', [$move,]);

        $this->hasPieceAtCoordinates($whiteQueen, $whiteQueenPosition)->shouldBe(true);
        $this->hasPieceAtCoordinates($whiteKnight, $whiteKnightPosition)->shouldBe(true);
    }

    function it_does_not_allow_to_move_piece_over_other_pieces()
    {
        $whiteBishop = Bishop::forColor(Piece\Color::white());
        $whiteBishopInitialPosition = CoordinatePair::fromFileAndRank('c', 1);

        $whitePawn = Pawn::forColor(Piece\Color::white());
        $whitePawnPosition = CoordinatePair::fromFileAndRank('d', 2);

        $destination = CoordinatePair::fromFileAndRank('e', 3);

        $this->placePieceAtCoordinates($whiteBishop, $whiteBishopInitialPosition);
        $this->placePieceAtCoordinates($whitePawn, $whitePawnPosition);

        $move = Chessboard\Move\AlongDiagonal::between($whiteBishopInitialPosition, $destination);

        $this->shouldThrow(new Chessboard\Exception\SquareIsOccupied($whitePawnPosition))->during('movePiece', [$move,]);

        $this->hasPieceAtCoordinates($whiteBishop, $whiteBishopInitialPosition)->shouldBe(true);
    }
}
