<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Fide\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;
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

    function it_is_chess_rule()
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

    function it_allows_white_pawn_move_forward_to_the_square_immediately_in_front_on_the_same_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 3))->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_allows_black_pawn_move_forward_to_the_square_immediately_in_front_on_the_same_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('b', 7),
            CoordinatePair::fromFileAndRank('b', 6)
        );

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 6))->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_pawn_move_forward_if_destination_position_is_occupied(Board $board, Rules $rules)
    {
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('c', 2),
            $destination
        );

        $board->isPositionOccupied($destination)->willReturn(true);

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_pawn_move_along_rank(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 3)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_pawn_move_along_diagonal(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('b', 5)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_pawn_move_to_another_square(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 5)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_pawn_move_along_file_backward(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('c', 3),
            CoordinatePair::fromFileAndRank('c', 4)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_allows_pawn_capture_opponents_piece_diagonally_in_front_of_it_on_an_adjacent_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('b', 3), Color::black())->willReturn(true);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_pawn_move_along_diagonal_if_is_not_capture(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('b', 3), Color::black())->willReturn(false);

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_pawn_move_backward_along_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_pawn_move_further_than_to_adjoining_square(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 5)
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_allows_pawn_advance_two_squares_along_the_same_file_on_first_move_provided_both_are_unoccupied(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('b', 7),
            CoordinatePair::fromFileAndRank('b', 5)
        );

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 6))->willReturn(false);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 5))->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_pawn_move_more_than_to_the_square_immediately_in_front_on_the_same_file_on_next_moves(Board $board, Rules $rules)
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
            $board,
            $rules
        );

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_pawn_advance_two_squares_if_any_is_occupied(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 4)
        );

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 4))->willReturn(false);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 3))->willReturn(true);

        $this->shouldThrow(new MoveOverInterveningPiece(CoordinatePair::fromFileAndRank('b', 3)))->during('apply', [$move, $board, $rules,]);
    }

    function it_allows_pawn_en_passant_capture_if_opponents_pawn_just_advanced_two_squares(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackPawn,
                    CoordinatePair::fromFileAndRank('c', 7),
                    CoordinatePair::fromFileAndRank('c', 5)
                )
            ),
            $board,
            $rules
        );

        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 5),
            CoordinatePair::fromFileAndRank('c', 6)
        );

        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('c', 6), Color::black())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_pawn_en_passant_capture_if_another_move_occurred(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Pawn::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('c', 7),
                    CoordinatePair::fromFileAndRank('c', 5)
                )
            ),
            $board,
            $rules
        );
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Pawn::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('g', 7),
                    CoordinatePair::fromFileAndRank('g', 6)
                )
            ),
            $board,
            $rules
        );

        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 5),
            CoordinatePair::fromFileAndRank('c', 6)
        );

        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('c', 6), Color::black())->willReturn(false);

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_pawn_en_passant_capture_if_moving_to_another_square(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('d', 4),
                    CoordinatePair::fromFileAndRank('d', 2)
                )
            ),
            $board,
            $rules
        );

        $move = new Move(
            $this->blackPawn,
            CoordinatePair::fromFileAndRank('c', 3),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('b', 2), Color::white())->willReturn(false);

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_captures_pawn_after_en_passant_move(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('d', 2),
                    CoordinatePair::fromFileAndRank('d', 4)
                )
            ),
            $board,
            $rules
        );

        $board->removePieceFrom(CoordinatePair::fromFileAndRank('d', 4))->shouldBeCalled()->willReturn($this->whitePawn);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackPawn,
                    CoordinatePair::fromFileAndRank('e', 4),
                    CoordinatePair::fromFileAndRank('d', 3)
                )
            ),
            $board,
            $rules
        )->shouldBeLike([new PieceWasCaptured($this->whitePawn, CoordinatePair::fromFileAndRank('d', 4)),]);
    }

    function it_disallows_exchange_by_default(Board $board, Rules $rules)
    {
        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('apply', [new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('c', 8)), $board, $rules,]);
    }

    function it_allows_exchange_when_white_pawn_reaches_rank_furthes_from_its_starting_position(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('d', 7),
                    CoordinatePair::fromFileAndRank('d', 8)
                )
            ),
            $board,
            $rules
        );

        $this->apply(new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('d', 8)), $board, $rules);
    }

    function it_allows_exchange_when_black_pawn_reaches_rank_furthes_from_its_starting_position(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackPawn,
                    CoordinatePair::fromFileAndRank('g', 2),
                    CoordinatePair::fromFileAndRank('g', 1)
                )
            ),
            $board,
            $rules
        );

        $this->apply(new Exchange(Queen::forColor(Color::black()), CoordinatePair::fromFileAndRank('g', 1)), $board, $rules);
    }

    function it_disallows_exchange_if_another_piece_reaches_furthest_position(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Queen::forColor(Color::white()),
                    CoordinatePair::fromFileAndRank('a', 2),
                    CoordinatePair::fromFileAndRank('a', 1)
                )
            ),
            $board,
            $rules
        );

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('apply', [new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('a', 1)), $board, $rules,]);
    }

    function it_disallows_more_than_one_exchange(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('b', 7),
                    CoordinatePair::fromFileAndRank('b', 8)
                )
            ),
            $board,
            $rules
        );

        $this->apply(new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 8)), $board, $rules);
        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('apply', [new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 8)), $board, $rules,]);
    }

    function it_disallows_exchange_to_another_pawn(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackPawn,
                    CoordinatePair::fromFileAndRank('g', 2),
                    CoordinatePair::fromFileAndRank('g', 1)
                )
            ),
            $board,
            $rules
        );

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('apply', [new Exchange(Pawn::forColor(Color::black()), CoordinatePair::fromFileAndRank('g', 1)), $board, $rules,]);
    }

    function it_disallows_exchange_to_a_king(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackPawn,
                    CoordinatePair::fromFileAndRank('g', 2),
                    CoordinatePair::fromFileAndRank('g', 1)
                )
            ),
            $board,
            $rules
        );

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('apply', [new Exchange(King::forColor(Color::black()), CoordinatePair::fromFileAndRank('g', 1)), $board, $rules,]);
    }
}
