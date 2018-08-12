<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Application\Dto;

use NicholasZyl\Chess\Application\Dto\Display;
use NicholasZyl\Chess\Application\Dto\PieceDto;
use PhpSpec\ObjectBehavior;

class PieceDtoSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('black', 'queen');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PieceDto::class);
    }

    function it_is_visualised_by_display(Display $display)
    {
        $display->visualisePiece('black', 'queen')->shouldBeCalled()->willReturn('black queen');

        $this->visualise($display)->shouldBe('black queen');
    }
}
