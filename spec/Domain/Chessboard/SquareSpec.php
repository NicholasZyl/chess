<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard;

use NicholasZyl\Chess\Domain\Chessboard\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard\Exception\SquareIsVacant;
use NicholasZyl\Chess\Domain\Chessboard\Square;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class SquareSpec extends ObjectBehavior
{
    /** @var Coordinates */
    private $coordinates;

    function let()
    {
        $this->coordinates = Coordinates::fromString('A1');
        $this->beConstructedThrough('forCoordinates', [$this->coordinates]);
    }

    function it_is_created_for_chessboard_coordinates()
    {
        $this->shouldHaveType(Square::class);
    }

    function it_allows_to_place_piece_on_it()
    {
        $piece = Piece::fromRankAndColor(
            Piece\Rank::king(),
            Color::white()
        );
        $this->place($piece);
    }

    function it_allows_to_pick_piece_placed_on_it()
    {
        $piece = Piece::fromRankAndColor(
            Piece\Rank::king(),
            Color::white()
        );
        $this->place($piece);
        $this->pick()->shouldBe($piece);
    }

    function it_does_not_allow_to_pick_a_piece_if_none_is_placed()
    {
        $this->shouldThrow(new SquareIsVacant($this->coordinates))->during('pick');
    }

    function it_is_vacant_after_piece_is_picked()
    {
        $piece = Piece::fromRankAndColor(
            Piece\Rank::king(),
            Color::white()
        );
        $this->place($piece);
        $this->pick();

        $this->shouldThrow(new SquareIsVacant($this->coordinates))->during('pick');
    }

    function it_allows_to_check_what_piece_is_placed_on_it()
    {
        $piece = Piece::fromRankAndColor(
            Piece\Rank::king(),
            Color::white()
        );
        $this->place($piece);

        $this->hasPlacedPiece($piece)->shouldBe(true);
    }

    function it_allows_to_check_its_coordinates()
    {
        $this->coordinates()->shouldBe($this->coordinates);
    }

    function it_allows_to_peek_what_piece_is_placed_on_it()
    {
        $piece = Piece::fromRankAndColor(
            Piece\Rank::king(),
            Color::white()
        );
        $this->place($piece);

        $this->peek()->shouldBeLike($piece);
    }
}
