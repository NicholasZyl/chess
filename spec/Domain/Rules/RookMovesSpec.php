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
use NicholasZyl\Chess\Domain\Piece\Rook;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\RookMoves;
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

    function it_is_chess_rule_for_piece_moves()
    {
        $this->shouldBeAnInstanceOf(PieceMovesRule::class);
    }

    function it_is_applicable_for_rook()
    {
        $this->isFor()->shouldBe(Rook::class);
    }

    function it_is_applicable_to_rook_move()
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );

        $this->isApplicableTo($move)->shouldBe(true);
    }

    function it_is_applicable_to_rook_move_check()
    {
        $move = new Action\CanMoveCheck(
            $this->rook,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
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

    function it_may_move_to_any_square_along_file_and_rank(Board $board)
    {
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $position = CoordinatePair::fromFileAndRank('b', 2);

        $this->getLegalDestinationsFrom(
            $this->rook, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('b', 4),
            CoordinatePair::fromFileAndRank('b', 5),
            CoordinatePair::fromFileAndRank('b', 6),
            CoordinatePair::fromFileAndRank('b', 7),
            CoordinatePair::fromFileAndRank('b', 8),
            CoordinatePair::fromFileAndRank('c', 2),
            CoordinatePair::fromFileAndRank('d', 2),
            CoordinatePair::fromFileAndRank('e', 2),
            CoordinatePair::fromFileAndRank('f', 2),
            CoordinatePair::fromFileAndRank('g', 2),
            CoordinatePair::fromFileAndRank('h', 2),
            CoordinatePair::fromFileAndRank('b', 1),
            CoordinatePair::fromFileAndRank('a', 2),

        ]);
    }

    function it_may_not_move_to_squares_over_intervening_piece(Board $board)
    {
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('c', 2))->willReturn(true);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 1))->willReturn(true);
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $position = CoordinatePair::fromFileAndRank('c', 3);

        $this->getLegalDestinationsFrom(
            $this->rook, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('c', 4),
            CoordinatePair::fromFileAndRank('c', 5),
            CoordinatePair::fromFileAndRank('c', 6),
            CoordinatePair::fromFileAndRank('c', 7),
            CoordinatePair::fromFileAndRank('c', 8),
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('e', 3),
            CoordinatePair::fromFileAndRank('f', 3),
            CoordinatePair::fromFileAndRank('g', 3),
            CoordinatePair::fromFileAndRank('h', 3),
            CoordinatePair::fromFileAndRank('c', 2),
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('a', 3),
        ]);
    }

    function it_may_not_move_to_squares_occupied_by_same_color(Board $board)
    {
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('c', 2))->willReturn(true);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('c', 2), Color::white())->willReturn(true);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 1))->willReturn(true);
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $position = CoordinatePair::fromFileAndRank('c', 3);

        $this->getLegalDestinationsFrom(
            $this->rook, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('c', 4),
            CoordinatePair::fromFileAndRank('c', 5),
            CoordinatePair::fromFileAndRank('c', 6),
            CoordinatePair::fromFileAndRank('c', 7),
            CoordinatePair::fromFileAndRank('c', 8),
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('e', 3),
            CoordinatePair::fromFileAndRank('f', 3),
            CoordinatePair::fromFileAndRank('g', 3),
            CoordinatePair::fromFileAndRank('h', 3),
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('a', 3),
        ]);
    }

    function it_allows_move_if_is_along_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 3)
        );
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_allows_move_if_is_along_rank(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('a', 3)
        );
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_move_if_is_along_diagonal(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->rook,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $board->isPositionOccupied(Argument::cetera())->willReturn(false);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_allows_move_if_is_not_over_other_pieces(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->rook,
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
            $this->rook,
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
