<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\RookMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RookMovesSpec extends ObjectBehavior
{
    /**
     * @var Rook
     */
    private $rook;

    function let()
    {
        $this->rook = Rook::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RookMoves::class);
    }

    function it_is_chess_rule()
    {
        $this->shouldBeAnInstanceOf(Rule::class);
    }

    function it_is_applicable_for_rook_move()
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );

        $this->isApplicable($move)->shouldBe(true);
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

    function it_allows_move_if_is_along_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );
        $board->isPositionOccupied(Argument::type(Board\Coordinates::class))->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_allows_move_if_is_along_rank(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('a', 3)
        );
        $board->isPositionOccupied(Argument::type(Board\Coordinates::class))->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_move_if_is_along_diagonal(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_allows_move_if_is_not_over_other_pieces(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('a', 2))->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_move_if_is_over_intervening_pieces(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('c', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $occupiedPosition = CoordinatePair::fromFileAndRank('c', 2);
        $board->isPositionOccupied($occupiedPosition)->willReturn(true);

        $this->shouldThrow(new MoveOverInterveningPiece($occupiedPosition))->during('apply', [$move, $board, $rules,]);
    }
}
