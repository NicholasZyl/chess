<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Fide\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules\MoveRule;
use PhpSpec\ObjectBehavior;

class PawnMovesSpec extends ObjectBehavior
{
    /**
     * @var Pawn
     */
    private $whitePawn;
    
    /**
     * @var Pawn
     */
    private $blackPawn;

    function let()
    {
        $this->whitePawn = Pawn::forColor(Color::white());
        $this->blackPawn = Pawn::forColor(Color::black());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PawnMoves::class);
    }

    function it_is_piece_moves_rule()
    {
        $this->shouldBeAnInstanceOf(MoveRule::class);
    }

    function it_has_standard_priority()
    {
        $this->priority()->shouldBe(10);
    }

    function it_is_applicable_for_white_pawn_move_forward_to_the_square_immediately_in_front_on_the_same_file()
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_applicable_for_black_pawn_move_forward_to_the_square_immediately_in_front_on_the_same_file()
    {
        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('b', 7),
            CoordinatePair::fromFileAndRank('b', 6)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_not_applicable_for_pawn_move_along_rank()
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_not_applicable_for_pawn_move_along_file_backward()
    {
        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('c', 3),
            CoordinatePair::fromFileAndRank('c', 4)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_applicable_for_pawn_capture_opponents_piece_diagonally_in_front_of_it_on_an_adjacent_file()
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_not_applicable_for_other_piece_move()
    {
        $move = new Move(
            Queen::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_not_applicable_to_move_pawn_backward_along_file()
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_not_applicable_for_move_pawn_further_than_to_adjoining_square()
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 5)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_applicable_for_pawn_advance_two_squares_along_the_same_file_on_first_move_provided_both_are_unoccupied()
    {
        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('b', 7),
            CoordinatePair::fromFileAndRank('b', 5)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_not_applicable_for_pawn_move_more_than_to_the_square_immediately_in_front_on_the_same_file_on_next_moves(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('a', 3),
            CoordinatePair::fromFileAndRank('a', 5)
        );

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('a', 2),
                    CoordinatePair::fromFileAndRank('a', 3)
                )
            ),
            $game
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_may_be_played_on_board_if_moving_along_file_and_destination_position_is_not_occupied(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('a', 3)
        );

        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('a', 3))->willReturn(false);

        $this->apply($move, $game);
    }

    function it_may_not_be_played_on_board_if_moving_along_file_and_destination_position_is_occupied(Game $game)
    {
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('c', 2),
            $destination
        );

        $game->isPositionOccupied($destination)->willReturn(true);

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_may_be_played_on_board_if_capturing_opponents_piece_diagonally_in_front_of_it_on_an_adjacent_file(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $game->isPositionOccupiedByOpponentOf(CoordinatePair::fromFileAndRank('b', 3), Color::white())->willReturn(true);

        $this->apply($move, $game);
    }

    function it_may_not_be_played_on_board_if_moving_along_diagonal_in_front_of_it_on_an_adjacent_file(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $game->isPositionOccupiedByOpponentOf(CoordinatePair::fromFileAndRank('b', 3), Color::white())->willReturn(false);

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_may_not_be_played_on_board_if_pawn_advance_two_squares_along_the_same_file_on_first_move_but_one_is_occupied(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 4)
        );

        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 4))->willReturn(false);
        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 3))->willReturn(true);

        $this->shouldThrow(new MoveOverInterveningPiece(CoordinatePair::fromFileAndRank('b', 3)))->during('apply', [$move, $game,]);
    }
}
