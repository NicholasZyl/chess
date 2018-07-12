<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Action;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class ExchangeSpec extends ObjectBehavior
{
    /**
     * @var Piece
     */
    private $piece;

    /**
     * @var Coordinates
     */
    private $position;

    function let()
    {
        $this->piece = Queen::forColor(Color::white());
        $this->position = CoordinatePair::fromFileAndRank('d', 8);

        $this->beConstructedWith(
            $this->piece,
            $this->position
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Exchange::class);
    }

    function it_is_game_action()
    {
        $this->shouldBeAnInstanceOf(Action::class);
    }

    function it_is_for_player_whom_piece_is_exchanged()
    {
        $this->player()->shouldBeLike(Color::white());
    }

    function it_is_to_happen_on_position()
    {
        $this->position()->shouldBe($this->position);
    }

    function it_has_the_piece_to_exchange_with()
    {
        $this->pieceToExchangeWith()->shouldBe($this->piece);
    }
}
