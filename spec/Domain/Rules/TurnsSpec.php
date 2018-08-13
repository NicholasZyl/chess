<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Action\CanMoveCheck;
use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ActionNotAllowed;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\Turns;
use PhpSpec\ObjectBehavior;

class TurnsSpec extends ObjectBehavior
{
    /**
     * @var Move
     */
    private $whiteMove;

    /**
     * @var Move
     */
    private $blackMove;

    function let()
    {
        $this->whiteMove = new Move(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 2), CoordinatePair::fromFileAndRank('a', 3));
        $this->blackMove = new Move(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('b', 7), CoordinatePair::fromFileAndRank('b', 5));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Turns::class);
    }

    function it_is_chess_rule()
    {
        $this->shouldBeAnInstanceOf(Rule::class);
    }

    function it_is_applicable_to_moves()
    {
        $this->isApplicableTo($this->whiteMove)->shouldBe(true);
    }

    function it_is_not_applicable_to_move_check()
    {
        $this->isApplicableTo(new CanMoveCheck(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 2), CoordinatePair::fromFileAndRank('a', 3)))->shouldBe(false);
    }

    function it_is_not_applicable_to_other_actions()
    {
        $this->isApplicableTo(new Exchange(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 8)))->shouldBe(false);
    }

    function it_allows_white_to_make_the_first_move(Board $board, Rules $rules)
    {
        $this->apply($this->whiteMove, $board, $rules);
    }

    function it_disallows_black_to_make_the_first_move(Board $board, Rules $rules)
    {
        $this->shouldThrow(ActionNotAllowed::class)->during('apply', [$this->blackMove, $board, $rules,]);
    }

    function it_allows_move_alternately(Board $board, Rules $rules)
    {
        $this->applyAfter(new Event\PieceWasMoved($this->whiteMove), $board, $rules)->shouldBeLike([]);
        $this->apply($this->blackMove, $board, $rules);
    }
}
