<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Application;

use NicholasZyl\Chess\Application\GameDto;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use PhpSpec\ObjectBehavior;

class GameDtoSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            [
                'a' => [
                    1 => null,
                    2 => Pawn::forColor(Color::white()),
                ],
                'b' => [
                    1 => null,
                    2 => null,
                ],
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GameDto::class);
    }

    function it_allows_checking_exact_position()
    {
        $this->position('a', 2)->shouldBeLike('white pawn');
    }
}
