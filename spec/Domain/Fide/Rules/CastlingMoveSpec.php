<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Rules;

use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Event\PieceWasPlaced;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\CastlingPrevented;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Fide\Rules\CastlingMove;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules\MoveRule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CastlingMoveSpec extends ObjectBehavior
{
    /**
     * @var King
     */
    private $whiteKing;

    /**
     * @var King
     */
    private $blackKing;

    function let(Game $game)
    {
        $this->whiteKing = King::forColor(Color::white());
        $this->blackKing = King::forColor(Color::black());

        $game->isPositionOccupied(Argument::any())->willReturn(false);
        $game->isPositionAttackedByOpponentOf(Argument::cetera())->willReturn(false);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CastlingMove::class);
    }

    function it_is_piece_moves_rule()
    {
        $this->shouldBeAnInstanceOf(MoveRule::class);
    }

    function it_has_high_priority()
    {
        $this->priority()->shouldBe(50);
    }

    function it_is_applicable_for_king_moving_two_squares_queenside()
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_applicable_for_king_moving_two_squares_kingside()
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('g', 1)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_is_not_applicable_for_king_move_not_along_rank()
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('e', 3)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_may_be_played_on_board(Game $game)
    {
        $move = new Move(
            $this->blackKing,
            CoordinatePair::fromFileAndRank('e', 8),
            CoordinatePair::fromFileAndRank('c', 8)
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('a', 8)
            ),
            $game
        );

        $this->apply($move, $game);
    }

    function it_may_not_be_played_if_king_that_already_moved(Game $game)
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
            $game
        );

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $game,]);
    }

    function it_may_be_played_if_other_king_already_moved(Game $game)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('a', 1)
            ),
            $game
        );

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    $this->blackKing,
                    CoordinatePair::fromFileAndRank('f', 1),
                    CoordinatePair::fromFileAndRank('e', 1)
                )
            ),
            $game
        );

        $this->apply($move, $game);
    }

    function it_may_not_be_played_if_rook_was_already_moved(Game $game)
    {
        $move = new Move(
            $this->blackKing,
            CoordinatePair::fromFileAndRank('e', 8),
            CoordinatePair::fromFileAndRank('c', 8)
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('a', 8)
            ),
            $game
        );

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('a', 8),
                    CoordinatePair::fromFileAndRank('a', 6)
                )
            ),
            $game
        );

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $game,]);
    }

    function it_may_not_be_played_if_rook_moved_back_to_initial_position(Game $game)
    {
        $move = new Move(
            $this->blackKing,
            CoordinatePair::fromFileAndRank('e', 8),
            CoordinatePair::fromFileAndRank('c', 8)
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('a', 8)
            ),
            $game
        );

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('a', 8),
                    CoordinatePair::fromFileAndRank('a', 6)
                )
            ),
            $game
        );

        $this->applyAfter(
            new PieceWasMoved(
                new Move(
                    Rook::forColor(Color::black()),
                    CoordinatePair::fromFileAndRank('a', 6),
                    CoordinatePair::fromFileAndRank('a', 8)
                )
            ),
            $game
        );

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $game,]);
    }

    function it_may_not_be_played_if_rook_was_captured(Game $game)
    {
        $move = new Move(
            $this->blackKing,
            CoordinatePair::fromFileAndRank('e', 8),
            CoordinatePair::fromFileAndRank('g', 8)
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('h', 8)
            ),
            $game
        );

        $this->applyAfter(
            new PieceWasCaptured(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('h', 8)
            ),
            $game
        );

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $game,]);
    }

    function it_may_not_be_played_if_king_is_attacked(Game $game)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('a', 1)
            ),
            $game
        );

        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('e', 1), Color::white())->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $game,]);
    }

    function it_may_not_be_played_if_position_king_must_cross_is_attacked(Game $game)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('a', 1)
            ),
            $game
        );

        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('e', 1), Color::white())->shouldBeCalled()->willReturn(false);
        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('d', 1), Color::white())->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $game,]);
    }

    function it_may_not_be_played_if_position_king_is_to_occupy_is_attacked(Game $game)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('g', 1)
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('h', 1)
            ),
            $game
        );

        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('e', 1), Color::white())->shouldBeCalled()->willReturn(false);
        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('f', 1), Color::white())->shouldBeCalled()->willReturn(false);
        $game->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('g', 1), Color::white())->shouldBeCalled()->willReturn(true);

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $game,]);
    }

    function it_may_not_be_played_if_there_is_a_piece_between_king_and_rook(Game $game)
    {
        $move = new Move(
            $this->whiteKing,
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('g', 1)
        );

        $this->applyAfter(
            new PieceWasPlaced(
                $this->whiteKing,
                CoordinatePair::fromFileAndRank('e', 1)
            ),
            $game
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::white()),
                CoordinatePair::fromFileAndRank('h', 1)
            ),
            $game
        );

        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('f', 1))->willReturn(false);
        $game->isPositionOccupied(CoordinatePair::fromFileAndRank('g', 1))->willReturn(true);

        $this->shouldThrow(new CastlingPrevented($move))->during('apply', [$move, $game,]);
    }

    function it_moves_rook_to_the_square_the_king_has_just_crossed_white_kingside(Game $game)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->whiteKing,
                CoordinatePair::fromFileAndRank('e', 1),
                CoordinatePair::fromFileAndRank('g', 1)
            )
        );

        $whiteRook = Rook::forColor(Color::white());

        $this->applyAfter(
            new PieceWasPlaced(
                $whiteRook,
                CoordinatePair::fromFileAndRank('h', 1)
            ),
            $game
        );

        $rookPosition = CoordinatePair::fromFileAndRank('h', 1);
        $rookDestination = CoordinatePair::fromFileAndRank('f', 1);
        $game->playMove($rookPosition, $rookDestination)->shouldBeCalled();

        $this->applyAfter($kingWasMoved, $game);
    }

    function it_moves_rook_to_the_square_the_king_has_just_crossed_white_queenside(Game $game)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->whiteKing,
                CoordinatePair::fromFileAndRank('e', 1),
                CoordinatePair::fromFileAndRank('c', 1)
            )
        );

        $whiteRook = Rook::forColor(Color::white());

        $this->applyAfter(
            new PieceWasPlaced(
                $whiteRook,
                CoordinatePair::fromFileAndRank('a', 1)
            ),
            $game
        );

        $rookPosition = CoordinatePair::fromFileAndRank('a', 1);
        $rookDestination = CoordinatePair::fromFileAndRank('d', 1);
        $rookWasMoved = [new PieceWasMoved(new Move($whiteRook, $rookPosition, $rookDestination)),];
        $game->playMove($rookPosition, $rookDestination)->shouldBeCalled()->willReturn($rookWasMoved);

        $this->applyAfter($kingWasMoved, $game)->shouldBeLike($rookWasMoved);
    }

    function it_moves_rook_to_the_square_the_king_has_just_crossed_black_kingside(Game $game)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->blackKing,
                CoordinatePair::fromFileAndRank('e', 8),
                CoordinatePair::fromFileAndRank('c', 8)
            )
        );

        $blackRook = Rook::forColor(Color::black());

        $this->applyAfter(
            new PieceWasPlaced(
                $blackRook,
                CoordinatePair::fromFileAndRank('a', 8)
            ),
            $game
        );

        $rookPosition = CoordinatePair::fromFileAndRank('a', 8);
        $rookDestination = CoordinatePair::fromFileAndRank('d', 8);
        $game->playMove($rookPosition, $rookDestination)->shouldBeCalled();

        $this->applyAfter($kingWasMoved, $game);
    }

    function it_moves_rook_to_the_square_the_king_has_just_crossed_black_queenside(Game $game)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->blackKing,
                CoordinatePair::fromFileAndRank('e', 8),
                CoordinatePair::fromFileAndRank('g', 8)
            )
        );

        $blackRook = Rook::forColor(Color::black());

        $this->applyAfter(
            new PieceWasPlaced(
                $blackRook,
                CoordinatePair::fromFileAndRank('h', 8)
            ),
            $game
        );

        $rookPosition = CoordinatePair::fromFileAndRank('h', 8);
        $rookDestination = CoordinatePair::fromFileAndRank('f', 8);
        $game->playMove($rookPosition, $rookDestination)->shouldBeCalled();

        $this->applyAfter($kingWasMoved, $game);
    }

    function it_does_not_move_rook_if_standard_king_move(Game $game)
    {
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->blackKing,
                CoordinatePair::fromFileAndRank('e', 8),
                CoordinatePair::fromFileAndRank('f', 8)
            )
        );

        $this->applyAfter(
            new PieceWasPlaced(
                Rook::forColor(Color::black()),
                CoordinatePair::fromFileAndRank('a', 8)
            ),
            $game
        );

        $game->playMove(Argument::cetera())->shouldNotBeCalled();

        $this->applyAfter($kingWasMoved, $game);
    }

    function it_is_applicable_to_rook_move_as_a_part_of_the_castling(Game $game)
    {
        $game->playMove(Argument::cetera())->shouldBeCalled();
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->whiteKing,
                CoordinatePair::fromFileAndRank('e', 1),
                CoordinatePair::fromFileAndRank('g', 1)
            )
        );
        $this->applyAfter($kingWasMoved, $game);

        $move = new Move(
            Rook::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('h', 1),
            CoordinatePair::fromFileAndRank('f', 1)
        );

        $this->isApplicable($move)->shouldBe(true);
    }

    function it_does_not_apply_for_standard_rook_move()
    {
        $move = new Move(
            Rook::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('e', 1)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_does_not_apply_for_next_rook_move_after_castling_is_done(Game $game)
    {
        $rook = Rook::forColor(Color::black());
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $rook,
                CoordinatePair::fromFileAndRank('a', 8),
                CoordinatePair::fromFileAndRank('d', 8)
            )
        );
        $this->applyAfter($kingWasMoved, $game);

        $move = new Move(
            $rook,
            CoordinatePair::fromFileAndRank('d', 8),
            CoordinatePair::fromFileAndRank('a', 8)
        );

        $this->isApplicable($move)->shouldBe(false);
    }

    function it_moves_rook_to_the_square_king_just_crossed_as_a_part_of_castling(Game $game)
    {
        $game->playMove(Argument::cetera())->shouldBeCalled();
        $kingWasMoved = new PieceWasMoved(
            new Move(
                $this->blackKing,
                CoordinatePair::fromFileAndRank('e', 8),
                CoordinatePair::fromFileAndRank('c', 8)
            )
        );
        $this->applyAfter($kingWasMoved, $game);

        $move = new Move(
            Rook::forColor(Color::black()),
            CoordinatePair::fromFileAndRank('a', 8),
            CoordinatePair::fromFileAndRank('d', 8)
        );

        $this->apply($move, $game);
    }
}