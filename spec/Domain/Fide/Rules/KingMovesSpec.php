<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Rules\KingMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;

class KingMovesSpec extends ObjectBehavior
{
    /**
     * @var King
     */
    private $king;

    function let()
    {
        $this->king = King::forColor(Color::white());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(KingMoves::class);
    }

    function it_is_chess_rule()
    {
        $this->shouldBeAnInstanceOf(Rule::class);
    }

    function it_is_applicable_for_king_move_along_diagonal()
    {
        $move = new Move(
            $this->king,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_applicable_for_king_move_along_file()
    {
        $move = new Move(
            $this->king,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_applicable_for_king_move_along_rank()
    {
        $move = new Move(
            $this->king,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_not_applicable_for_king_move_not_along_known_direction()
    {
        $move = new Move(
            $this->king,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_not_applicable_for_king_move_further_than_to_adjoining_square()
    {
        $move = new Move(
            $this->king,
            CoordinatePair::fromFileAndRank('a', 3),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_not_applicable_for_other_piece_move()
    {
        $move = new Move(
            Knight::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 2)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_not_applicable_for_not_move_action()
    {
        $action = new class implements Action {};

        $this->isApplicable($action)->shouldBe(false);
    }

    function it_may_be_played_on_board(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->king,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2)
        );

        $this->apply($move, $board, $rules);
    }

    function it_may_not_be_played_if_not_applicable(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->king,
            CoordinatePair::fromFileAndRank('c', 1),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }
}
