<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece\Rook;
use NicholasZyl\Chess\Domain\PieceFactory;
use PhpSpec\ObjectBehavior;

class PieceFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PieceFactory::class);
    }

    function it_creates_piece_from_its_rank_and_color()
    {
        $this->createPieceNamedForColor('pawn', 'white')->shouldBeLike(Pawn::forColor(Color::white()));
        $this->createPieceNamedForColor('knight', 'white')->shouldBeLike(Knight::forColor(Color::white()));
        $this->createPieceNamedForColor('bishop', 'white')->shouldBeLike(Bishop::forColor(Color::white()));
        $this->createPieceNamedForColor('rook', 'white')->shouldBeLike(Rook::forColor(Color::white()));
        $this->createPieceNamedForColor('queen', 'white')->shouldBeLike(Queen::forColor(Color::white()));
        $this->createPieceNamedForColor('king', 'white')->shouldBeLike(King::forColor(Color::white()));
    }

    function it_fails_to_create_piece_if_unknown_rank()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createPieceNamedForColor', ['unknown', Color::white(),]);
    }

    function it_creates_piece_from_its_description()
    {
        $this->createPieceFromDescription('white pawn')->shouldBeLike(Pawn::forColor(Color::white()));
    }

    function it_fails_to_create_piece_if_description_is_missing_rank_as_second_word()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createPieceFromDescription', ['white',]);
    }

    function it_fails_to_create_piece_if_description_is_missing_color_as_first_word()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createPieceFromDescription', ['pawn',]);
    }
}
