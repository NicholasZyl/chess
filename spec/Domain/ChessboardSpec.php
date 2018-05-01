<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Chessboard;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class ChessboardSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Chessboard::class);
    }

    public function it_allows_placing_piece_at_given_coordinates()
    {
        $piece = Piece::fromRankAndColor(Piece\Rank::fromString('king'), Color::fromString('white'));
        $coordinates = Coordinates::fromString('B2');

        $this->placePieceAtCoordinates($piece, $coordinates);
    }

    public function it_allows_moving_piece_from_one_coordinate_to_another()
    {
        $source = Coordinates::fromString('B2');
        $destination = Coordinates::fromString('C2');
        $this->movePiece($source, $destination);
    }
}
