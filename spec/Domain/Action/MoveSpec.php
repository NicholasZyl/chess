<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Action;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Action\Movement;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use PhpSpec\ObjectBehavior;

class MoveSpec extends ObjectBehavior
{
    /**
     * @var Piece
     */
    private $piece;

    /**
     * @var Coordinates
     */
    private $source;

    /**
     * @var Coordinates
     */
    private $destination;

    function let()
    {
        $this->piece = Pawn::forColor(Color::white());
        $this->source = CoordinatePair::fromFileAndRank('b', 2);
        $this->destination = CoordinatePair::fromFileAndRank('b', 3);

        $this->beConstructedWith($this->piece, $this->source, $this->destination);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Move::class);
    }

    function it_is_game_action()
    {
        $this->shouldBeAnInstanceOf(Action::class);
    }

    function it_is_piece_movement_action()
    {
        $this->shouldBeAnInstanceOf(Movement::class);
    }

    function it_is_for_player_whom_piece_is_moved()
    {
        $this->player()->shouldBeLike(Color::white());
    }

    function it_is_for_piece()
    {
        $this->piece()->shouldBeLike($this->piece);
    }

    function it_has_source_position()
    {
        $this->source()->shouldBeLike($this->source);
    }

    function it_has_destination_position()
    {
        $this->destination()->shouldBeLike($this->destination);
    }

    function it_knows_if_is_along_given_direction()
    {
        $this->inDirection(new AlongFile())->shouldBe(true);
    }

    function it_knows_when_is_not_along_given_direction()
    {
        $this->inDirection(new AlongRank())->shouldBe(false);
    }

    function it_knows_when_is_over_given_distance()
    {
        $this->isOverDistanceOf(1)->shouldBe(true);
    }

    function it_knows_when_is_not_over_given_distance()
    {
        $this->isOverDistanceOf(3)->shouldBe(false);
    }

    function it_is_not_over_given_distance_if_in_unknown_direction()
    {
        $this->beConstructedWith($this->piece, $this->source, CoordinatePair::fromFileAndRank('e', 4));

        $this->isOverDistanceOf(3)->shouldBe(false);
    }
}
