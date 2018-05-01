<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Rules;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Rules;
use NicholasZyl\Chess\Domain\Chessboard\Rules\Chess;
use NicholasZyl\Chess\Domain\Chessboard\Rules\PieceMovementRules;
use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class ChessSpec extends ObjectBehavior
{
    function let(PieceMovementRules $pieceMovementRules)
    {
        $pieceMovementRules->isFor()->shouldBeCalled()->willReturn(Piece\Rank::fromString('king'));

        $this->beConstructedWith(
            [
                $pieceMovementRules,
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Chess::class);
    }

    function it_is_a_rules_set()
    {
        $this->shouldBeAnInstanceOf(Rules::class);
    }

    function it_validates_if_given_piece_move_is_legal(PieceMovementRules $pieceMovementRules)
    {
        $from = Square::forCoordinates(Coordinates::fromString('a1'));
        $from->place(
            Piece::fromRankAndColor(
                Piece\Rank::fromString('king'),
                Piece\Color::white()
            )
        );
        $to = Square::forCoordinates(Coordinates::fromString('a2'));

        $pieceMovementRules->validate(Coordinates::fromString('a1'), Coordinates::fromString('a2'))->shouldBeCalled();

        $this->validateMove($from, $to);
    }
}
