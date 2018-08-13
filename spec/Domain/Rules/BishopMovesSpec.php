<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\BishopMoves;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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

    function it_is_chess_rule_for_piece_moves()
    {
        $this->shouldBeAnInstanceOf(PieceMovesRule::class);
    }

    function it_is_applicable_for_bishop()
    {
        $this->isFor()->shouldBe(Bishop::class);
    }

    function it_is_applicable_to_bishop_move()
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicableTo($move)->shouldBe(true);
    }

    function it_is_applicable_to_bishop_move_check()
    {
        $move = new Action\CanMoveCheck(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicableTo($move)->shouldBe(true);
    }

    function it_is_not_applicable_to_other_piece_move()
    {
        $move = new Move(
            Knight::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicableTo($move)->shouldBe(false);
    }

    function it_is_not_applicable_to_exchanges()
    {
        $exchange = new Exchange(Knight::forColor(Color::white()), CoordinatePair::fromFileAndRank('f', 4));
        $this->isApplicableTo($exchange)->shouldBe(false);
    }

    function it_may_move_to_any_square_along_diagonal(Board $board)
    {
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $position = CoordinatePair::fromFileAndRank('c', 3);

        $this->getLegalDestinationsFrom(
            $this->bishop, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('d', 4),
            CoordinatePair::fromFileAndRank('e', 5),
            CoordinatePair::fromFileAndRank('f', 6),
            CoordinatePair::fromFileAndRank('g', 7),
            CoordinatePair::fromFileAndRank('h', 8),
            CoordinatePair::fromFileAndRank('d', 2),
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 4),
            CoordinatePair::fromFileAndRank('a', 5),
        ]);
    }

    function it_may_not_move_to_squares_over_intervening_piece(Board $board)
    {
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('e', 5))->willReturn(true);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('d', 2))->willReturn(true);
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $position = CoordinatePair::fromFileAndRank('c', 3);

        $this->getLegalDestinationsFrom(
            $this->bishop, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('d', 4),
            CoordinatePair::fromFileAndRank('e', 5),
            CoordinatePair::fromFileAndRank('d', 2),
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 4),
            CoordinatePair::fromFileAndRank('a', 5),
        ]);
    }

    function it_may_not_move_to_squares_occupied_by_same_color(Board $board)
    {
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('e', 5))->willReturn(true);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('e', 5), Color::white())->willReturn(true);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('d', 2))->willReturn(true);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('d', 2), Color::white())->willReturn(false);
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);

        $position = CoordinatePair::fromFileAndRank('c', 3);

        $this->getLegalDestinationsFrom(
            $this->bishop, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('d', 4),
            CoordinatePair::fromFileAndRank('d', 2),
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 4),
            CoordinatePair::fromFileAndRank('a', 5),
        ]);
    }

    function it_allows_move_if_is_along_diagonal(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $board->isPositionOccupied(Argument::any())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_move_if_is_along_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );
        $board->isPositionOccupied(Argument::any())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_move_if_is_along_rank(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );
        $board->isPositionOccupied(Argument::any())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_move_if_is_not_over_other_pieces(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $board->isPositionOccupied(Argument::any())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_move_if_is_over_intervening_pieces(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->bishop,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $board->isPositionOccupied(Argument::any())->willReturn(false);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 2))->willReturn(true);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('b', 2), Color::white())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }
}
