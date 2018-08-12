<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Application\Dto;

use NicholasZyl\Chess\Application\Dto\BoardDto;
use NicholasZyl\Chess\Application\Dto\Display;
use NicholasZyl\Chess\Application\Dto\GameDto;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use PhpSpec\ObjectBehavior;

class GameDtoSpec extends ObjectBehavior
{
    /**
     * @var BoardDto
     */
    private $boardDto;

    function let()
    {
        $this->boardDto = new BoardDto(
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
        $this->beConstructedWith(
            $this->boardDto
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GameDto::class);
    }

    function it_has_board_representation()
    {
        $this->board()->shouldBeLike($this->boardDto);
    }

    function it_knows_when_is_not_ended_yet()
    {
        $this->isEnded()->shouldBe(false);
    }

    function it_knows_when_is_already_ended()
    {
        $this->beConstructedWith(
            $this->boardDto,
            null,
            true
        );
        $this->isEnded()->shouldBe(true);
    }

    function it_knows_when_color_is_not_checkmated()
    {
        $this->checked()->shouldBeNull();
    }

    function it_knows_when_color_is_checkmated()
    {
        $this->beConstructedWith(
            $this->boardDto,
            'white'
        );
        $this->checked()->shouldBe('white');
    }

    function it_knows_when_there_is_no_winner()
    {
        $this->winner()->shouldBeNull();
    }

    function it_knows_the_winner()
    {
        $this->beConstructedWith(
            $this->boardDto,
            'white',
            true,
            'black'
        );
        $this->winner()->shouldBe('black');
    }

    function it_is_visualised_by_display(Display $display)
    {
        $display->visualiseGame($this->getWrappedObject())->shouldBeCalled()->willReturn('visualisation');

        $this->visualise($display)->shouldBe('visualisation');
    }
}
