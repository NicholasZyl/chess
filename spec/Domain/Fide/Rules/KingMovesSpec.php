<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\CastlingPrevented;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\KingMoves;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\PieceMovesRule;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class KingMovesSpec extends ObjectBehavior
{
    /**
     * @var King
     */
    private $whiteKing;

    /**
     * @var King
     */
    private $blackKing;

    function let()
    {
        $this->whiteKing = King::forColor(Color::white());
        $this->blackKing = King::forColor(Color::black());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(KingMoves::class);
    }

    function it_is_applicable_for_king()
    {
        $this->isFor()->shouldBe(King::class);
    }

    function it_is_chess_rule_for_piece_moves()
    {
        $this->shouldBeAnInstanceOf(PieceMovesRule::class);
    }

    function it_is_applicable_to_king_move()
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2)
        );

        $this->isApplicableTo($move)->shouldBe(true);
    }

    function it_is_not_applicable_to_other_piece_move()
    {
        $move = new Move(
            Knight::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('c', 2)
        );

        $this->isApplicableTo($move)->shouldBe(false);
    }

    function it_is_not_applicable_to_not_move_action()
    {
        $action = new class implements Action {};

        $this->isApplicableTo($action)->shouldBe(false);
    }

    function it_may_move_to_any_adjoining_square(Board $board)
    {
        $position = CoordinatePair::fromFileAndRank('c', 3);

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->whiteKing, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('c', 4),
            CoordinatePair::fromFileAndRank('d', 4),
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('d', 2),
            CoordinatePair::fromFileAndRank('c', 2),
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('b', 4),
        ]);
    }

    function it_may_not_move_to_square_occupied_by_same_color(Board $board)
    {
        $position = CoordinatePair::fromFileAndRank('c', 3);

        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('c', 4), Color::black())->willReturn(true);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('d', 4), Color::black())->willReturn(true);
        $board->isPositionOccupiedBy(CoordinatePair::fromFileAndRank('b', 2), Color::black())->willReturn(true);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->blackKing, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('d', 2),
            CoordinatePair::fromFileAndRank('c', 2),
            CoordinatePair::fromFileAndRank('b', 3),
            CoordinatePair::fromFileAndRank('b', 4),
        ]);
    }

    function it_may_move_by_castling_from_starting_position(Board $board)
    {
        $position = CoordinatePair::fromFileAndRank('e', 1);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->whiteKing, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('e', 2),
            CoordinatePair::fromFileAndRank('f', 2),
            CoordinatePair::fromFileAndRank('f', 1),
            CoordinatePair::fromFileAndRank('d', 1),
            CoordinatePair::fromFileAndRank('d', 2),
            CoordinatePair::fromFileAndRank('c', 1),
            CoordinatePair::fromFileAndRank('g', 1),
        ]);
    }

    function it_may_not_move_by_castling_if_king_already_moved(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackKing,
                    CoordinatePair::fromFileAndRank('f', 8),
                    CoordinatePair::fromFileAndRank('e', 8)
                )
            ),
            $board,
            $rules
        );

        $position = CoordinatePair::fromFileAndRank('e', 8);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->blackKing, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('f', 8),
            CoordinatePair::fromFileAndRank('f', 7),
            CoordinatePair::fromFileAndRank('e', 7),
            CoordinatePair::fromFileAndRank('d', 7),
            CoordinatePair::fromFileAndRank('d', 8),
        ]);
    }

    function it_may_not_move_by_castling_if_rook_already_moved(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('a', 8),
                    CoordinatePair::fromFileAndRank('a', 7)
                )
            ),
            $board,
            $rules
        );

        $position = CoordinatePair::fromFileAndRank('e', 8);
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->getLegalDestinationsFrom(
            $this->blackKing, $position, $board
        )->shouldYieldLike([
            CoordinatePair::fromFileAndRank('f', 8),
            CoordinatePair::fromFileAndRank('f', 7),
            CoordinatePair::fromFileAndRank('e', 7),
            CoordinatePair::fromFileAndRank('d', 7),
            CoordinatePair::fromFileAndRank('d', 8),
            CoordinatePair::fromFileAndRank('g', 8),
        ]);
    }

    function it_allows_move_if_is_along_file(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2)
        );
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_allows_move_if_is_along_rank(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_move_if_is_not_along_known_direction(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('d', 3),
            CoordinatePair::fromFileAndRank('c', 1)
        );
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_move_if_is_further_than_to_adjoining_square(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('a', 3),
            CoordinatePair::fromFileAndRank('c', 3)
        );
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_allows_king_castling_move(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->blackKing,
            CoordinatePair::fromFileAndRank('e', 8),
            CoordinatePair::fromFileAndRank('c', 8)
        );

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);
        $board->isPositionOccupied(Argument::any())->willReturn(false);
        $board->isPositionAttackedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_castling_move_if_king_has_already_moved(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->whiteKing,
                    CoordinatePair::fromFileAndRank('f', 1),
                    CoordinatePair::fromFileAndRank('e', 1)
                )
            ),
            $board,
            $rules
        );
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_allows_castling_move_if_other_king_has_moved(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackKing,
                    CoordinatePair::fromFileAndRank('f', 1),
                    CoordinatePair::fromFileAndRank('e', 1)
                )
            ),
            $board,
            $rules
        );

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);
        $board->isPositionOccupied(Argument::any())->willReturn(false);
        $board->isPositionAttackedBy(Argument::cetera())->willReturn(false);

        $this->apply($move, $board, $rules);
    }

    function it_disallows_castling_move_if_rook_was_already_moved(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('a', 8),
                    CoordinatePair::fromFileAndRank('a', 6)
                )
            ),
            $board,
            $rules
        );

        $move = new Move(
            $this->blackKing,
            CoordinatePair::fromFileAndRank('e', 8),
            CoordinatePair::fromFileAndRank('c', 8)
        );
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_disallows_castling_move_if_rook_was_captured(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->blackKing,
            CoordinatePair::fromFileAndRank('e', 8),
            CoordinatePair::fromFileAndRank('g', 8)
        );

        $this->applyAfter(
            new PieceWasCaptured(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('h', 8)
            ),
            $board,
            $rules
        );
        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);

        $this->shouldThrow(MoveToIllegalPosition::class)->during('apply', [$move, $board, $rules,]);
    }

    function it_temporarily_prevents_castling_move_if_king_is_attacked(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);
        $board->isPositionOccupied(Argument::any())->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_temporarily_prevents_castling_move_if_position_king_must_cross_is_attacked(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);
        $board->isPositionOccupied(Argument::any())->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('d', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_temporarily_prevents_castling_move_if_position_king_is_to_occupy_is_attacked(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('g', 1)
        );

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);
        $board->isPositionOccupied(Argument::any())->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('e', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('f', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(false);
        $board->isPositionAttackedBy(CoordinatePair::fromFileAndRank('g', 1), Color::black(), $rules)->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_temporarily_prevents_castling_move_if_there_is_a_piece_between_king_and_rook(Board $board, Rules $rules)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('g', 1)
        );

        $board->isPositionOccupiedBy(Argument::cetera())->willReturn(false);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('f', 1))->willReturn(false);
        $board->isPositionOccupied(CoordinatePair::fromFileAndRank('g', 1))->willReturn(true);

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $board, $rules,]);
    }

    function it_plays_rook_move_to_the_square_the_king_has_just_crossed_white_kingside_after_king_castling_move(Board $board, Rules $rules)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->whiteKing,
                CoordinatePair::fromFileAndRank('e', 1),
                CoordinatePair::fromFileAndRank('g', 1)
            )
        );

        $rookPosition = CoordinatePair::fromFileAndRank('h', 1);
        $rookDestination = CoordinatePair::fromFileAndRank('f', 1);
        $whiteRook = Rook::forColor(Color::white());
        $board->pickPieceFrom($rookPosition)->shouldBeCalled()->willReturn($whiteRook);
        $board->placePieceAt($whiteRook, $rookDestination)->shouldBeCalled();
        $rookWasMoved = [new PieceWasMoved(new Move($whiteRook, $rookPosition, $rookDestination)),];

        $this->applyAfter($kingWasMoved, $board, $rules)->shouldBeLike($rookWasMoved);
    }

    function it_plays_rook_move_to_the_square_the_king_has_just_crossed_white_queenside_after_king_castling_move(Board $board, Rules $rules)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->whiteKing,
                CoordinatePair::fromFileAndRank('e', 1),
                CoordinatePair::fromFileAndRank('c', 1)
            )
        );

        $whiteRook = Rook::forColor(Color::white());

        $rookPosition = CoordinatePair::fromFileAndRank('a', 1);
        $rookDestination = CoordinatePair::fromFileAndRank('d', 1);
        $board->pickPieceFrom($rookPosition)->shouldBeCalled()->willReturn($whiteRook);
        $board->placePieceAt($whiteRook, $rookDestination)->shouldBeCalled();
        $rookWasMoved = [new PieceWasMoved(new Move($whiteRook, $rookPosition, $rookDestination)),];

        $this->applyAfter($kingWasMoved, $board, $rules)->shouldBeLike($rookWasMoved);
    }

    function it_plays_rook_move_to_the_square_the_king_has_just_crossed_black_kingside_after_king_castling_move(Board $board, Rules $rules)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->blackKing,
                CoordinatePair::fromFileAndRank('e', 8),
                CoordinatePair::fromFileAndRank('c', 8)
            )
        );

        $rookPosition = CoordinatePair::fromFileAndRank('a', 8);
        $rookDestination = CoordinatePair::fromFileAndRank('d', 8);
        $blackRook = Rook::forColor(Color::black());
        $board->pickPieceFrom($rookPosition)->shouldBeCalled()->willReturn($blackRook);
        $board->placePieceAt($blackRook, $rookDestination)->shouldBeCalled();
        $rookWasMoved = [new PieceWasMoved(new Move($blackRook, $rookPosition, $rookDestination)),];

        $this->applyAfter($kingWasMoved, $board, $rules)->shouldBeLike($rookWasMoved);
    }

    function it_plays_rook_move_to_the_square_the_king_has_just_crossed_black_queenside_after_king_castling_move(Board $board, Rules $rules)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->blackKing,
                CoordinatePair::fromFileAndRank('e', 8),
                CoordinatePair::fromFileAndRank('g', 8)
            )
        );

        $rookPosition = CoordinatePair::fromFileAndRank('h', 8);
        $rookDestination = CoordinatePair::fromFileAndRank('f', 8);
        $blackRook = Rook::forColor(Color::black());
        $board->pickPieceFrom($rookPosition)->shouldBeCalled()->willReturn($blackRook);
        $board->placePieceAt($blackRook, $rookDestination)->shouldBeCalled();
        $rookWasMoved = [new PieceWasMoved(new Move($blackRook, $rookPosition, $rookDestination)),];

        $this->applyAfter($kingWasMoved, $board, $rules)->shouldBeLike($rookWasMoved);
    }

    function it_does_not_play_rook_move_if_it_was_standard_king_move(Board $board, Rules $rules)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->blackKing,
                CoordinatePair::fromFileAndRank('e', 8),
                CoordinatePair::fromFileAndRank('f', 8)
            )
        );

        $board->pickPieceFrom(Argument::cetera())->shouldNotBeCalled();
        $board->placePieceAt(Argument::cetera())->shouldNotBeCalled();

        $this->applyAfter($kingWasMoved, $board, $rules)->shouldBeLike([]);
    }
}
