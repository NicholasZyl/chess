<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Fide\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rule;
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
        $this->shouldBeAnInstanceOf(Rule::class);
    }

    function it_has_standard_priority()
    {
        $this->priority()->shouldBe(10);
    }

    function it_is_applicable_to_pawn_move()
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_not_applicable_to_other_piece_move()
    {
        $move = new Move(
            Queen::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_is_applicable_to_exchange_action()
    {
        $this->isApplicable(new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('c', 8)))->shouldBe(true);
    }

    function it_allows_white_pawn_move_forward_to_the_square_immediately_in_front_on_the_same_file(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 3))->willReturn(false);

        $this->apply($move, $game);
    }

    function it_allows_black_pawn_move_forward_to_the_square_immediately_in_front_on_the_same_file(Game $game)
    {
        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('b', 7),
            CoordinatePair::fromFileAndRank('b', 6)
        );

        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 6))->willReturn(false);

        $this->apply($move, $game);
    }

    function it_disallows_pawn_move_forward_if_destination_position_is_occupied(Game $game)
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

    function it_disallows_pawn_move_along_rank(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_disallows_pawn_move_along_diagonal(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('b', 5)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_disallows_pawn_move_to_another_square(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 5)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_disallows_pawn_move_along_file_backward(Game $game)
    {
        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('c', 3),
            CoordinatePair::fromFileAndRank('c', 4)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_allows_pawn_capture_opponents_piece_diagonally_in_front_of_it_on_an_adjacent_file(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $game->isPositionOccupiedByOpponentOf(CoordinatePair::fromFileAndRank('b', 3), Color::white())->willReturn(true);

        $this->apply($move, $game);
    }

    function it_disallows_pawn_move_along_diagonal_if_is_not_capture(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $game->isPositionOccupiedByOpponentOf(CoordinatePair::fromFileAndRank('b', 3), Color::white())->willReturn(false);

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_disallows_pawn_move_backward_along_file(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_disallows_pawn_move_further_than_to_adjoining_square(Game $game)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 5)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_allows_pawn_advance_two_squares_along_the_same_file_on_first_move_provided_both_are_unoccupied(Game $game)
    {
        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('b', 7),
            CoordinatePair::fromFileAndRank('b', 5)
        );

        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 6))->willReturn(false);
        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 5))->willReturn(false);

        $this->apply($move, $game);
    }

    function it_disallows_pawn_move_more_than_to_the_square_immediately_in_front_on_the_same_file_on_next_moves(Game $game)
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

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_disallows_pawn_advance_two_squares_if_any_is_occupied(Game $game)
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

    function it_allows_pawn_en_passant_capture_if_opponents_pawn_just_advanced_two_squares(Game $game)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackPawn,
                    CoordinatePair::fromFileAndRank('c', 7),
                    CoordinatePair::fromFileAndRank('c', 5)
                )
            ),
            $game
        );

        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 5),
            CoordinatePair::fromFileAndRank('c', 6)
        );

        $game->isPositionOccupiedByOpponentOf(CoordinatePair::fromFileAndRank('c', 6), Color::white())->willReturn(false);

        $this->apply($move, $game);
    }

    function it_disallows_pawn_en_passant_capture_if_another_move_occurred(Game $game)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Pawn::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('c', 7),
                    CoordinatePair::fromFileAndRank('c', 5)
                )
            ),
            $game
        );
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Pawn::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('g', 7),
                    CoordinatePair::fromFileAndRank('g', 6)
                )
            ),
            $game
        );

        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 5),
            CoordinatePair::fromFileAndRank('c', 6)
        );

        $game->isPositionOccupiedByOpponentOf(CoordinatePair::fromFileAndRank('c', 6), Color::white())->willReturn(false);

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_disallows_pawn_en_passant_capture_if_moving_to_another_square(Game $game)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('d', 4),
                    CoordinatePair::fromFileAndRank('d', 2)
                )
            ),
            $game
        );

        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('c', 3),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $game->isPositionOccupiedByOpponentOf(CoordinatePair::fromFileAndRank('b', 2), Color::black())->willReturn(false);

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $game,]);
    }

    function it_captures_pawn_after_en_passant_move(Game $game)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('d', 2),
                    CoordinatePair::fromFileAndRank('d', 4)
                )
            ),
            $game
        );

        $game->removePieceFromBoard(CoordinatePair::fromFileAndRank('d', 4))->shouldBeCalled()->willReturn($this->whitePawn);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackPawn,
                    CoordinatePair::fromFileAndRank('e', 4),
                    CoordinatePair::fromFileAndRank('d', 3)
                )
            ),
            $game
        )->shouldBeLike([new PieceWasCaptured($this->whitePawn, CoordinatePair::fromFileAndRank('d', 4)),]);
    }

    function it_disallows_exchange_by_default(Game $game)
    {
        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('apply', [new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('c', 8)), $game,]);
    }

    function it_allows_exchange_when_white_pawn_reaches_rank_furthes_from_its_starting_position(Game $game)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('d', 7),
                    CoordinatePair::fromFileAndRank('d', 8)
                )
            ),
            $game
        );

        $this->apply(new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('d', 8)), $game);
    }

    function it_allows_exchange_when_black_pawn_reaches_rank_furthes_from_its_starting_position(Game $game)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackPawn,
                    CoordinatePair::fromFileAndRank('g', 2),
                    CoordinatePair::fromFileAndRank('g', 1)
                )
            ),
            $game
        );

        $this->apply(new Exchange(Queen::forColor(Color::black()), CoordinatePair::fromFileAndRank('g', 1)), $game);
    }

    function it_disallows_exchange_if_another_piece_reaches_furthest_position(Game $game)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Queen::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('a', 2),
                    CoordinatePair::fromFileAndRank('a', 1)
                )
            ),
            $game
        );

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('apply', [new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 1)), $game,]);
    }

    function it_disallows_more_than_one_exchange(Game $game)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('b', 7),
                    CoordinatePair::fromFileAndRank('b', 8)
                )
            ),
            $game
        );

        $this->apply(new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 8)), $game);
        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('apply', [new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 8)), $game,]);
    }
}
