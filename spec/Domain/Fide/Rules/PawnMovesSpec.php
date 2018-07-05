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
use NicholasZyl\Chess\Domain\Fide\Event\PawnReachedPromotion;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Fide\Rules\PawnMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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

    function it_is_chess_rule_for_piece_moves()
    {
        $this->shouldBeAnInstanceOf(PieceMovesRule::class);
    }

    function it_is_applicable_to_pawn_move()
    {
        $move = new Move(
            $this->whitePawn,
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $this->isApplicableTo($move)->shouldBe(true);
    }

    function it_is_not_applicable_to_other_piece_move()
    {
        $move = new Move(
            Queen::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2)
        );

        $this->isApplicableTo($move)->shouldBe(false);
    }

    function it_is_applicable_to_exchange_action()
    {
        $this->isApplicableTo(new Exchange(Queen::forColor(Color::white()), CoordinatePair::fromFileAndRank('c', 8)))->shouldBe(true);
    }

    function it_is_applicable_for_pawn()
    {
        $this->isApplicableFor($this->blackPawn)->shouldBe(true);
    }

    function it_is_not_applicable_for_other_piece()
    {
        $this->isApplicableFor(Knight::forColor(Color::white()))->shouldBe(false);
    }

    function it_may_move_white_to_the_square_immediately_in_front_on_the_same_file(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('c', 3);

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('c', 4))->willReturn(false);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->whitePawn, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('c', 4),
        ]);
    }

    function it_may_move_black_to_the_square_immediately_in_front_on_the_same_file(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('c', 3);

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('c', 2))->willReturn(false);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->blackPawn, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('c', 2),
        ]);
    }

    function it_may_not_move_to_the_square_immediately_in_front_on_the_same_file_if_it_is_occupied(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('c', 3);

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('c', 2))->willReturn(true);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->blackPawn, $position, $board
        )->shouldYieldLike([]);
    }

    function it_may_capture_along_diagonal_forward(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('e', 5);

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('e', 4))->willReturn(false);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('d', 4), Color::white())->willReturn(false);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('f', 4), Color::white())->willReturn(true);

        $this->getLegalDestinationsFrom(
            $this->blackPawn, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('e', 4),
            CoordinatePair::fromFileAndRank('f', 4),
        ]);
    }

    function it_may_advance_two_squares_along_the_same_file_on_first_move_provided_both_are_unoccupied(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('b', 2);

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 3))->willReturn(false);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 4))->willReturn(false);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->whitePawn, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('b', 4),
        ]);
    }

    function it_may_not_advance_two_squares_along_the_same_file_on_first_move_if_any_is_occupied(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('b', 2);

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 3))->willReturn(false);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('b', 4))->willReturn(true);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->whitePawn, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('b', 3),
        ]);
    }

    function it_may_not_advance_two_squares_after_it_moved(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('a', 3))->willReturn(false);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whitePawn,
                    CoordinatePair::fromFileAndRank('a', 1),
                    CoordinatePair::fromFileAndRank('a', 2)
                )
            ),
            $board,
            $rules
        );

        $this->getLegalDestinationsFrom(
            $this->whitePawn, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('a', 3),
        ]);
    }

    function it_may_en_passant_capture(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('f', 5);

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('f', 6))->willReturn(true);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Pawn::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('g', 7),
                    CoordinatePair::fromFileAndRank('g', 5)
                )
            ),
            $board,
            $rules
        );

        $this->getLegalDestinationsFrom(
            $this->whitePawn, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('g', 6),
        ]);
    }

    function it_may_not_en_passant_capture_if_another_move_occured(Board $board, Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('f', 5);

        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('f', 6))->willReturn(true);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Pawn::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('g', 7),
                    CoordinatePair::fromFileAndRank('g', 5)
                )
            ),
            $board,
            $rules
        );
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Pawn::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('a', 7),
                    CoordinatePair::fromFileAndRank('a', 6)
                )
            ),
            $board,
            $rules
        );

        $this->getLegalDestinationsFrom(
            $this->whitePawn, $position, $board
        )->shouldYieldLike([]);
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

    function it_is_to_be_promoted_when_reaches_furhtest_rank_from_its_starting_position(Board $board, Rules $rules)
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
        )->shouldBeLike([new PawnReachedPromotion($this->blackPawn, CoordinatePair::fromFileAndRank('g', 1))]);
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
