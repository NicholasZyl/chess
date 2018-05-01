<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Exception\IllegalMove;
use NicholasZyl\Chess\Domain\Board\Rules;
use NicholasZyl\Chess\Domain\Board\Square;
use NicholasZyl\Chess\Domain\Chessboard;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ChessboardSpec extends ObjectBehavior
{
    function let(Rules $rules)
    {
        $this->beConstructedWith($rules);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Chessboard::class);
    }

    function it_allows_placing_piece_at_given_coordinates()
    {
        $piece = Piece::fromRankAndColor(Piece\Rank::fromString('king'), Color::fromString('white'));
        $coordinates = Coordinates::fromString('B2');

        $this->placePieceAtCoordinates($piece, $coordinates);
    }

    function it_allows_moving_piece_from_one_coordinate_to_another()
    {
        $source = Coordinates::fromString('B2');
        $destination = Coordinates::fromString('C2');

        $piece = Piece::fromRankAndColor(Piece\Rank::fromString('king'), Color::fromString('white'));
        $this->placePieceAtCoordinates($piece, $source);

        $this->movePiece($source, $destination);

        $this->hasPieceAtCoordinates($piece, $source)->shouldBe(false);
        $this->hasPieceAtCoordinates($piece, $destination)->shouldBe(true);
    }

    function it_knows_what_piece_is_placed_on_square_at_given_coordinates()
    {
        $piece = Piece::fromRankAndColor(Piece\Rank::fromString('king'), Color::fromString('white'));
        $coordinates = Coordinates::fromString('B2');
        $this->placePieceAtCoordinates($piece, $coordinates);

        $this->hasPieceAtCoordinates($piece, $coordinates)->shouldBe(true);
    }

    function it_does_not_allow_move_that_is_illegal_according_to_given_rules(Rules $rules)
    {
        $source = Coordinates::fromString('B2');
        $destination = Coordinates::fromString('C2');

        $piece = Piece::fromRankAndColor(Piece\Rank::fromString('king'), Color::fromString('white'));
        $this->placePieceAtCoordinates($piece, $source);

        $from = Square::forCoordinates($source);
        $from->place($piece);

        $illegalMove = new IllegalMove($source, $destination);
        $rules->validateMove(Argument::exact($from), Argument::exact(Square::forCoordinates($destination)))->willThrow($illegalMove);

        $this->shouldThrow($illegalMove)->during('movePiece', [$source, $destination,]);
    }
}
