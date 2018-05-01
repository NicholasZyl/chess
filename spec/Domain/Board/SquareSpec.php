<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Exception\SquareIsVacant;
use NicholasZyl\Chess\Domain\Board\Square;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
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

    public function it_is_created_for_chessboard_coordinates()
    {
        $this->shouldHaveType(Square::class);
    }

    public function it_allows_to_place_piece_on_it()
    {
        $piece = Piece::fromRankAndColor(
            Piece\Rank::fromString('king'),
            Color::white()
        );
        $this->place($piece);
    }

    public function it_allows_to_pick_piece_placed_on_it()
    {
        $piece = Piece::fromRankAndColor(
            Piece\Rank::fromString('king'),
            Color::white()
        );
        $this->place($piece);
        $this->pick()->shouldBe($piece);
    }

    public function it_does_not_allow_to_pick_a_piece_if_none_is_placed()
    {
        $this->shouldThrow(new SquareIsVacant($this->coordinates))->during('pick');
    }

    public function it_is_vacant_after_piece_is_picked()
    {
        $piece = Piece::fromRankAndColor(
            Piece\Rank::fromString('king'),
            Color::white()
        );
        $this->place($piece);
        $this->pick();

        $this->shouldThrow(new SquareIsVacant($this->coordinates))->during('pick');
    }
}
