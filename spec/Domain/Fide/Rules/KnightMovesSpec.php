<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Fide\Rules\KnightMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class KnightMovesSpec extends ObjectBehavior
{
    /**
     * @var Knight
     */
    private $knight;

    function let()
    {
        $this->knight = Knight::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(KnightMoves::class);
    }

    function it_is_chess_rule_for_piece_moves()
    {
        $this->shouldBeAnInstanceOf(PieceMovesRule::class);
    }

    function it_is_applicable_to_knight_move()
    {
        $move = new Move(
            $this->knight,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 2)
        );

        $this->isApplicableTo($move)->shouldBe(true);
    }

    function it_is_not_applicable_to_other_piece_move()
    {
        $move = new Move(
            Queen::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicableTo($move)->shouldBe(false);
    }

    function it_is_not_applicable_to_not_move_action()
    {
        $action = new class implements Action {};

        $this->isApplicableTo($action)->shouldBe(false);
    }

    function it_is_applicable_for_knight()
    {
        $this->isApplicableFor($this->knight)->shouldBe(true);
    }

    function it_is_not_applicable_for_other_piece()
    {
        $this->isApplicableFor(Bishop::forColor(Color::white()))->shouldBe(false);
    }

    function it_may_move_to_nearest_position_not_on_same_file_nor_rank_nor_diagonal(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('c', 3);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->knight, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('d', 5),
            CoordinatePair::fromFileAndRank('e', 4),
            CoordinatePair::fromFileAndRank('e', 2),
            CoordinatePair::fromFileAndRank('d', 1),
            CoordinatePair::fromFileAndRank('b', 1),
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 4),
            CoordinatePair::fromFileAndRank('b', 5),
        ]);
    }

    function it_may_not_move_to_square_occupied_by_same_color(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('c', 3);

        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('e', 4), Color::white())->willReturn(true);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('e', 2), Color::white())->willReturn(true);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('a', 2), Color::white())->willReturn(true);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->knight, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('d', 5),
            CoordinatePair::fromFileAndRank('d', 1),
            CoordinatePair::fromFileAndRank('b', 1),
            CoordinatePair::fromFileAndRank('a', 4),
            CoordinatePair::fromFileAndRank('b', 5),
        ]);
    }

    function it_may_not_move_if_all_squares_are_occupied_by_same_color(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('c', 3);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(true);

        $this->getLegalDestinationsFrom(
            $this->knight, $position, $board
        )->shouldYieldLike([]);
    }

    function it_disallows_move_if_is_along_diagonal(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->knight,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_move_if_is_along_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->knight,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_move_if_is_along_rank(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->knight,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('a', 3)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_allows_move_if_is_to_nearest_position_not_on_same_file_nor_rank_nor_diagonal(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->knight,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 2)
        );

        $this->apply($move, $board, $rules);
    }

    function it_disallows_move_if_is_not_applicable(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->knight,
            CoordinatePair::fromFileAndRank('c', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }
}
