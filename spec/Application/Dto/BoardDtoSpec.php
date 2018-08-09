<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Application\Dto;

use NicholasZyl\Chess\Application\Dto\BoardDto;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use PhpSpec\ObjectBehavior;

class BoardDtoSpec extends ObjectBehavior
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
        $this->shouldHaveType(BoardDto::class);
    }

    function it_allows_to_check_exact_position()
    {
        $this->position('a', 2)->shouldBeLike('white pawn');
    }
}
