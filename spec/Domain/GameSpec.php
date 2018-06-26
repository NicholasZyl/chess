<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasExchanged;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\PositionOccupiedByAnotherColor;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveExposesToCheck;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Queen;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\InitialPositions;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;

class GameSpec extends ObjectBehavior
{
    function let(Board $board, InitialPositions $initialPositions, Rules $rules)
    {
        $this->beConstructedWith($board, $initialPositions, $rules);
        $initialPositions->initialiseBoard($board);
    }

    function it_initialises_board_with_provided_initial_positions_of_pieces(Board $board, InitialPositions $initialPositions)
    {
        $initialPositions->initialiseBoard($board)->shouldBeCalled();

        $this->shouldHaveType(Game::class);
    }

    function it_moves_piece_from_one_position_to_another(Board $board, Rules $rules)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFrom($source)->shouldBeCalled()->willReturn($pawn);
        $rules->applyRulesTo($move, $this->getWrappedObject())->shouldBeCalled();

        $board->placePieceAt($pawn, $destination)->shouldBeCalled()->willReturn([]);
        $rules->applyAfter(new PieceWasMoved($move), $this->getWrappedObject())->shouldBeCalled();

        $this->playMove($source, $destination)->shouldBeLike([new PieceWasMoved($move),]);
    }

    function it_places_piece_back_if_move_is_illegal(Board $board, Rules $rules)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFrom($source)->shouldBeCalled()->willReturn($pawn);
        $illegalMove = new MoveToIllegalPosition($move);
        $rules->applyRulesTo($move, $this->getWrappedObject())->shouldBeCalled()->willThrow($illegalMove);
        $board->placePieceAt($pawn, $destination)->shouldNotBeCalled();
        $board->placePieceAt($pawn, $source)->shouldBeCalled();

        $this->shouldThrow($illegalMove)->during('playMove', [$source, $destination,]);
    }

    function it_does_not_allow_moving_piece_if_no_rules_are_applicable(Board $board, Rules $rules)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFrom($source)->shouldBeCalled()->willReturn($pawn);
        $rules->applyRulesTo($move, $this->getWrappedObject())->willThrow(new NoApplicableRule());
        $board->placePieceAt($pawn, $destination)->shouldNotBeCalled();
        $board->placePieceAt($pawn, $source)->shouldBeCalled();

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('playMove', [$source, $destination,]);
    }

    function it_applies_events_happened_after_move(Board $board, Rules $rules)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFrom($source)->shouldBeCalled()->willReturn($pawn);
        $rules->applyRulesTo($move, $this->getWrappedObject())->shouldBeCalled();
        $board->placePieceAt($pawn, $destination)->shouldBeCalled()->willReturn([]);
        $anotherEvent = new PieceWasMoved(new Move(Rook::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 3), $source));
        $rules->applyAfter(new PieceWasMoved($move), $this->getWrappedObject())->shouldBeCalled()->willReturn([$anotherEvent]);

        $this->playMove($source, $destination)->shouldBeLike([new PieceWasMoved($move), $anotherEvent,]);
    }

    function it_fails_when_trying_to_move_piece_from_not_occupied_coordinates(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 1);

        $unoccupiedSquare = new SquareIsUnoccupied($source);
        $board->pickPieceFrom($source)->willThrow($unoccupiedSquare);

        $this->shouldThrow($unoccupiedSquare)->during('playMove', [$source, $destination,]);
    }

    function it_fails_when_trying_to_move_piece_to_occupied_coordinates(Board $board, Rules $rules)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFrom($source)->shouldBeCalled()->willReturn($pawn);
        $rules->applyRulesTo($move, $this->getWrappedObject())->shouldBeCalled();
        $board->placePieceAt($pawn, $destination)->shouldBeCalled()->willThrow(new SquareIsOccupied($destination));
        $board->placePieceAt($pawn, $source)->shouldBeCalled();
        $rules->applyAfter(new PieceWasMoved($move), $this->getWrappedObject())->shouldNotBeCalled();

        $this->shouldThrow(new MoveToOccupiedPosition($destination))->during('playMove', [$source, $destination,]);
    }

    function it_reverts_move_if_it_is_exposing_to_check(Board $board, Rules $rules)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('d', 3);
        $move = new Move($pawn, $source, $destination);

        $capturedPiece = Bishop::forColor(Color::black());

        $board->pickPieceFrom($source)->shouldBeCalled()->willReturn($pawn);
        $rules->applyRulesTo($move, $this->getWrappedObject())->shouldBeCalled();
        $pieceWasCaptured = new PieceWasCaptured($capturedPiece, $destination);
        $board->placePieceAt($pawn, $destination)->shouldBeCalled()->willReturn([$pieceWasCaptured,]);
        $rules->applyAfter($pieceWasCaptured, $this->getWrappedObject())->shouldBeCalled()->willReturn([]);
        $moveExposesToCheck = new MoveExposesToCheck();
        $rules->applyAfter(new PieceWasMoved($move), $this->getWrappedObject())->shouldBeCalled()->willThrow($moveExposesToCheck);

        $board->pickPieceFrom($destination)->shouldBeCalled()->willReturn($pawn);
        $board->placePieceAt($pawn, $source)->shouldBeCalled()->willReturn([]);
        $board->placePieceAt($capturedPiece, $destination)->shouldBeCalled()->willReturn([]);

        $this->shouldThrow($moveExposesToCheck)->during('playMove', [$source, $destination,]);
    }

    function it_knows_if_given_piece_is_occupied(Board $board)
    {
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $board->isPositionOccupied($position)->shouldBeCalled()->willReturn(true);

        $this->isPositionOccupied($position)->shouldBe(true);
    }

    function it_knows_if_given_piece_is_occupied_by_opponent_color(Board $board)
    {
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $color = Color::white();
        $board->isPositionOccupiedBy($position, $color)->shouldBeCalled()->willReturn(true);

        $this->isPositionOccupiedByOpponentOf($position, Color::black())->shouldBe(true);
    }

    function it_knows_if_position_is_attacked(Board $board)
    {
        $player = Color::white();
        $opponent = Color::black();
        $position = CoordinatePair::fromFileAndRank('b', 3);

        $board->isPositionAttackedBy($position, $opponent, $this->getWrappedObject())->shouldBeCalled()->willReturn(true);

        $this->isPositionAttackedByOpponentOf($position, $player)->shouldBe(true);
    }

    function it_knows_when_piece_may_be_moved(Rules $rules)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $rules->applyRulesTo($move, $this->getWrappedObject())->shouldBeCalled();

        $this->mayMove($pawn, $source, $destination)->shouldBe(true);
    }

    function it_knows_when_piece_may_not_be_moved(Rules $rules)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $rules->applyRulesTo($move, $this->getWrappedObject())->shouldBeCalled()->willThrow(new MoveToIllegalPosition($move));

        $this->mayMove($pawn, $source, $destination)->shouldBe(false);
    }

    function it_removes_piece_from_a_board_at_given_position(Board $board)
    {
        $piece = Pawn::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);

        $board->removePieceFrom($position)->shouldBeCalled()->willReturn($piece);

        $this->removePieceFromBoard($position)->shouldBe($piece);
    }

    function it_allows_to_exchange_piece_on_board(Board $board, Rules $rules)
    {
        $piece = Queen::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $action = new Exchange($piece, $position);

        $rules->applyRulesTo($action, $this->getWrappedObject())->shouldBeCalled();

        $pieceWasExchanged = new PieceWasExchanged(Pawn::forColor(Color::white()), $piece, $position);
        $board->exchangePieceOnPositionTo($position, $piece)->shouldBeCalled()->willReturn([$pieceWasExchanged,]);

        $this->exchangePieceOnBoardTo($position, $piece)->shouldBeLike([$pieceWasExchanged,]);
    }

    function it_is_not_allowed_to_exchange_piece_if_it_failed_on_board(Board $board, Rules $rules)
    {
        $piece = Queen::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $action = new Exchange($piece, $position);

        $rules->applyRulesTo($action, $this->getWrappedObject())->shouldBeCalled();

        $board->exchangePieceOnPositionTo($position, $piece)->shouldBeCalled()->willThrow(new PositionOccupiedByAnotherColor($position, Color::black()));

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('exchangePieceOnBoardTo', [$position, $piece,]);
    }

    function it_is_not_allowed_to_exchange_piece_if_no_rule_is_applicable(Board $board, Rules $rules)
    {
        $piece = Queen::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);

        $rules->applyRulesTo(new Exchange($piece, $position), $this->getWrappedObject())->shouldBeCalled()->willThrow(new NoApplicableRule());
        $board->exchangePieceOnPositionTo($position, $piece)->shouldNotBeCalled();

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('exchangePieceOnBoardTo', [$position, $piece,]);
    }

    function it_is_not_allowed_to_exchange_piece_if_rule_does_not_allow(Board $board, Rules $rules)
    {
        $piece = Queen::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $action = new Exchange($piece, $position);

        $rules->applyRulesTo($action, $this->getWrappedObject())->shouldBeCalled()->willThrow(new ExchangeIsNotAllowed($position));
        $board->exchangePieceOnPositionTo($position, $piece)->shouldNotBeCalled();

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('exchangePieceOnBoardTo', [$position, $piece,]);
    }
}
