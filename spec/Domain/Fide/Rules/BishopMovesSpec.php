<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Rules\BishopMoves;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rule;
use PhpSpec\ObjectBehavior;

class BishopMovesSpec extends ObjectBehavior
{
    /**
     * @var Bishop
     */
    private $bishop;

    function let()
    {
        $this->bishop = Bishop::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BishopMoves::class);
    }

    function it_is_piece_moves_rule()
    {
        $this->shouldBeAnInstanceOf(Rule::class);
    }

    function it_has_standard_priority()
    {
        $this->priority()->shouldBe(10);
    }

    function it_is_applicable_for_bishop_move_along_diagonal()
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_not_applicable_for_bishop_move_along_file()
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_not_applicable_for_bishop_move_along_rank()
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('a', 3)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_not_applicable_for_other_piece_move()
    {
        $move = new Move(
            Knight::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_not_applicable_for_not_move_action()
    {
        $action = new class implements Action {};

        $this->isApplicable($action)->shouldBe(false);
    }

    function it_may_be_played_on_board_if_not_over_other_pieces(Game $game)
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 2))->willReturn(false);

        $this->apply($move, $game);
    }

    function it_may_not_be_played_over_intervening_pieces(Game $game)
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $occupiedPosition = CoordinatePair::fromFileAndRank('b', 2);
        $game->isPositionOccupied($occupiedPosition)->willReturn(true);

        $this->shouldThrow(new MoveOverInterveningPiece($occupiedPosition))->during('apply', [$move, $game,]);
    }
}
