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
use NicholasZyl\Chess\Domain\Piece\Knight;
use NicholasZyl\Chess\Domain\Piece\Queen;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\QueenMoves;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QueenMovesSpec extends ObjectBehavior
{
    /**
     * @var Queen
     */
    private $queen;

    function let()
    {
        $this->queen = Queen::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(QueenMoves::class);
    }

    function it_is_chess_rule_for_piece_moves()
    {
        $this->shouldBeAnInstanceOf(PieceMovesRule::class);
    }

    function it_is_applicable_for_queen()
    {
        $this->isFor()->shouldBe(Queen::class);
    }

    function it_is_applicable_to_queen_move()
    {
        $move = new Move(
            $this->queen,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicableTo($move)->shouldBe(true);
    }

    function it_is_applicable_to_queen_move_check()
    {
        $move = new Action\CanMoveCheck(
            $this->queen,
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

    function it_may_move_to_any_square_along_file_rank_and_diagonal(Board $board)
    {
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $position = CoordinatePair::fromFileAndRank('b', 2);

        $this->getLegalDestinationsFrom(
            $this->queen, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('b', 4),
            CoordinatePair::fromFileAndRank('b', 5),
            CoordinatePair::fromFileAndRank('b', 6),
            CoordinatePair::fromFileAndRank('b', 7),
            CoordinatePair::fromFileAndRank('b', 8),

            CoordinatePair::fromFileAndRank('c', 3),
            CoordinatePair::fromFileAndRank('d', 4),
            CoordinatePair::fromFileAndRank('e', 5),
            CoordinatePair::fromFileAndRank('f', 6),
            CoordinatePair::fromFileAndRank('g', 7),
            CoordinatePair::fromFileAndRank('h', 8),

            CoordinatePair::fromFileAndRank('c', 2),
            CoordinatePair::fromFileAndRank('d', 2),
            CoordinatePair::fromFileAndRank('e', 2),
            CoordinatePair::fromFileAndRank('f', 2),
            CoordinatePair::fromFileAndRank('g', 2),
            CoordinatePair::fromFileAndRank('h', 2),

            CoordinatePair::fromFileAndRank('c', 1),

            CoordinatePair::fromFileAndRank('b', 1),

            CoordinatePair::fromFileAndRank('a', 1),

            CoordinatePair::fromFileAndRank('a', 2),

            CoordinatePair::fromFileAndRank('a', 3),
        ]);
    }

    function it_may_not_move_to_squares_over_intervening_piece(Board $board)
    {
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('c', 3))->willReturn(true);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('c', 2))->willReturn(true);
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $position = CoordinatePair::fromFileAndRank('b', 2);

        $this->getLegalDestinationsFrom(
            $this->queen, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('b', 4),
            CoordinatePair::fromFileAndRank('b', 5),
            CoordinatePair::fromFileAndRank('b', 6),
            CoordinatePair::fromFileAndRank('b', 7),
            CoordinatePair::fromFileAndRank('b', 8),

            CoordinatePair::fromFileAndRank('c', 3),

            CoordinatePair::fromFileAndRank('c', 2),

            CoordinatePair::fromFileAndRank('c', 1),

            CoordinatePair::fromFileAndRank('b', 1),

            CoordinatePair::fromFileAndRank('a', 1),

            CoordinatePair::fromFileAndRank('a', 2),

            CoordinatePair::fromFileAndRank('a', 3),
        ]);
    }

    function it_may_not_move_to_squares_occupied_by_same_color(Board $board)
    {
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('e', 3))->willReturn(true);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('e', 3), Color::white())->willReturn(true);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('e', 2))->willReturn(true);
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $position = CoordinatePair::fromFileAndRank('d', 2);

        $this->getLegalDestinationsFrom(
            $this->queen, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('d', 4),
            CoordinatePair::fromFileAndRank('d', 5),
            CoordinatePair::fromFileAndRank('d', 6),
            CoordinatePair::fromFileAndRank('d', 7),
            CoordinatePair::fromFileAndRank('d', 8),

            CoordinatePair::fromFileAndRank('e', 2),

            CoordinatePair::fromFileAndRank('e', 1),

            CoordinatePair::fromFileAndRank('d', 1),

            CoordinatePair::fromFileAndRank('c', 1),

            CoordinatePair::fromFileAndRank('c', 2),
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('a', 2),

            CoordinatePair::fromFileAndRank('c', 3),
            CoordinatePair::fromFileAndRank('b', 4),
            CoordinatePair::fromFileAndRank('a', 5),
        ]);
    }

    function it_allows_move_if_is_along_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->queen,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 5)
        );
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_allows_move_if_is_along_rank(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->queen,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_allows_move_if_is_along_diagonal(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->queen,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_move_if_is_not_along_known_direction(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->queen,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 1)
        );
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_allows_move_if_is_not_over_other_pieces(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->queen,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_move_if_is_over_intervening_pieces(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->queen,
            CoordinatePair::fromFileAndRank('c', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);
        $occupiedPosition = CoordinatePair::fromFileAndRank('c', 2);
        $board->isPositionOccupied($occupiedPosition)->willReturn(true);
        $board->isPositionOccupiedBy($occupiedPosition, Color::white())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }
}
