<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Event\PieceWasPlaced;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Game;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Piece\InitialPositions;
use NicholasZyl\Chess\Domain\Rules\MoveRule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GameSpec extends ObjectBehavior
{
    function let(Board $board, InitialPositions $initialPositions, MoveRule $moveRule)
    {
        $this->beConstructedWith($board, $initialPositions, [$moveRule,]);
        $initialPositions->initialiseBoard($board)->willReturn([]);
        $moveRule->applyAfter(Argument::cetera())->willReturn([]);
    }

    function it_initialises_board_with_provided_initial_positions_of_pieces(Board $board, InitialPositions $initialPositions, MoveRule $moveRule)
    {
        $pieceWasPlaced = new PieceWasPlaced(Pawn::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 2));
        $initialPositions->initialiseBoard($board)->shouldBeCalled()->willReturn([$pieceWasPlaced,]);
        $moveRule->applyAfter($pieceWasPlaced, $this->getWrappedObject())->shouldBeCalled();

        $this->shouldHaveType(Game::class);
    }

    function it_moves_piece_from_one_position_to_another(Board $board, MoveRule $moveRule)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFromCoordinates($source)->shouldBeCalled()->willReturn($pawn);
        $moveRule->isApplicable($move)->shouldBeCalled()->willReturn(true);
        $moveRule->apply($move, $this->getWrappedObject())->shouldBeCalled();
        $pieceWasPlaced = new PieceWasPlaced($pawn, $destination);
        $board->placePieceAtCoordinates($pawn, $destination)->shouldBeCalled()->willReturn([$pieceWasPlaced,]);
        $moveRule->applyAfter($pieceWasPlaced, $this->getWrappedObject())->shouldBeCalled();
        $moveRule->applyAfter(new PieceWasMoved($move), $this->getWrappedObject())->shouldBeCalled();

        $this->playMove($source, $destination)->shouldBeLike([$pieceWasPlaced, new PieceWasMoved($move),]);
    }

    function it_places_piece_back_if_move_is_illegal(Board $board, MoveRule $moveRule)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFromCoordinates($source)->shouldBeCalled()->willReturn($pawn);
        $moveRule->isApplicable($move)->shouldBeCalled()->willReturn(true);
        $illegalMove = new MoveToIllegalPosition($move);
        $moveRule->apply($move, $this->getWrappedObject())->shouldBeCalled()->willThrow($illegalMove);
        $board->placePieceAtCoordinates($pawn, $destination)->shouldNotBeCalled();
        $board->placePieceAtCoordinates($pawn, $source)->shouldBeCalled();

        $this->shouldThrow($illegalMove)->during('playMove', [$source, $destination,]);
    }

    function it_does_not_allow_moving_piece_if_no_rules_are_applicable(Board $board, MoveRule $moveRule)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFromCoordinates($source)->shouldBeCalled()->willReturn($pawn);
        $moveRule->isApplicable($move)->shouldBeCalled()->willReturn(false);
        $board->placePieceAtCoordinates($pawn, $destination)->shouldNotBeCalled();
        $board->placePieceAtCoordinates($pawn, $source)->shouldBeCalled();

        $this->shouldThrow(new MoveToIllegalPosition($move))->during('playMove', [$source, $destination,]);
    }

    function it_applies_only_most_important_applicable_rule(Board $board, InitialPositions $initialPositions, MoveRule $rule, MoveRule $lessImportantRule)
    {
        $this->beConstructedWith($board, $initialPositions, [$lessImportantRule, $rule,]);

        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $lessImportantRule->isApplicable($move)->shouldBeCalled()->willReturn(true);
        $lessImportantRule->apply($move, $this->getWrappedObject())->shouldNotBeCalled();
        $lessImportantRule->priority()->willReturn(10);

        $rule->isApplicable($move)->shouldBeCalled()->willReturn(true);
        $rule->apply($move, $this->getWrappedObject())->shouldBeCalled();
        $rule->priority()->willReturn(50);

        $board->pickPieceFromCoordinates($source)->shouldBeCalled()->willReturn($pawn);
        $pieceWasPlaced = new PieceWasPlaced($pawn, $destination);
        $board->placePieceAtCoordinates($pawn, $destination)->shouldBeCalled()->willReturn([$pieceWasPlaced,]);

        $rule->applyAfter(Argument::cetera())->shouldBeCalled();
        $lessImportantRule->applyAfter(Argument::cetera())->shouldBeCalled();

        $this->playMove($source, $destination)->shouldBeLike([$pieceWasPlaced, new PieceWasMoved($move),]);
    }

    function it_applies_events_happened_after_move(Board $board, MoveRule $moveRule)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFromCoordinates($source)->shouldBeCalled()->willReturn($pawn);
        $moveRule->isApplicable($move)->shouldBeCalled()->willReturn(true);
        $moveRule->apply($move, $this->getWrappedObject())->shouldBeCalled();
        $pieceWasPlaced = new PieceWasPlaced($pawn, $destination);
        $board->placePieceAtCoordinates($pawn, $destination)->shouldBeCalled()->willReturn([$pieceWasPlaced,]);
        $moveRule->applyAfter($pieceWasPlaced, $this->getWrappedObject())->shouldBeCalled();
        $anotherEvent = new PieceWasMoved(new Move(Rook::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 3), $source));
        $moveRule->applyAfter(new PieceWasMoved($move), $this->getWrappedObject())->shouldBeCalled()->willReturn([$anotherEvent]);

        $this->playMove($source, $destination)->shouldBeLike([$pieceWasPlaced, new PieceWasMoved($move), $anotherEvent,]);
    }

    function it_fails_when_trying_to_move_piece_from_not_occupied_coordinates(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 1);

        $unoccupiedSquare = new SquareIsUnoccupied($source);
        $board->pickPieceFromCoordinates($source)->willThrow($unoccupiedSquare);

        $this->shouldThrow($unoccupiedSquare)->during('playMove', [$source, $destination,]);
    }

    function it_fails_when_trying_to_move_piece_to_occupied_coordinates(Board $board, MoveRule $moveRule)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $board->pickPieceFromCoordinates($source)->shouldBeCalled()->willReturn($pawn);
        $moveRule->isApplicable($move)->shouldBeCalled()->willReturn(true);
        $moveRule->apply($move, $this->getWrappedObject())->shouldBeCalled();
        $board->placePieceAtCoordinates($pawn, $destination)->shouldBeCalled()->willThrow(new SquareIsOccupied($destination));
        $board->placePieceAtCoordinates($pawn, $source)->shouldBeCalled();
        $moveRule->applyAfter(new PieceWasMoved($move), $this->getWrappedObject())->shouldNotBeCalled();

        $this->shouldThrow(new MoveToOccupiedPosition($destination))->during('playMove', [$source, $destination,]);
    }

    function it_knows_if_given_piece_is_occupied(Board $board)
    {
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $board->isPositionOccupied($position)->shouldBeCalled()->willReturn(true);

        $this->isPositionOccupied($position)->shouldBe(true);
    }

    function it_knows_if_given_piece_is_occupied_by_opponent(Board $board)
    {
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $color = Color::white();
        $board->isPositionOccupiedByOpponentOf($position, $color)->shouldBeCalled()->willReturn(true);

        $this->isPositionOccupiedByOpponentOf($position, $color)->shouldBe(true);
    }

    function it_knows_if_position_is_attacked(Board $board)
    {
        $player = Color::white();
        $opponent = Color::black();
        $position = CoordinatePair::fromFileAndRank('b', 3);

        $board->isPositionAttackedBy($position, $opponent, $this->getWrappedObject())->shouldBeCalled()->willReturn(true);

        $this->isPositionAttackedByOpponentOf($position, $player)->shouldBe(true);
    }

    function it_knows_when_piece_may_be_moved(MoveRule $moveRule)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $moveRule->isApplicable($move)->shouldBeCalled()->willReturn(true);
        $moveRule->apply($move, $this->getWrappedObject())->shouldBeCalled();

        $this->mayMove($pawn, $source, $destination)->shouldBe(true);
    }

    function it_knows_when_piece_may_not_be_moved(MoveRule $moveRule)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new Move($pawn, $source, $destination);

        $moveRule->isApplicable($move)->shouldBeCalled()->willReturn(true);
        $moveRule->apply($move, $this->getWrappedObject())->shouldBeCalled()->willThrow(new MoveToIllegalPosition($move));

        $this->mayMove($pawn, $source, $destination)->shouldBe(false);
    }

    function it_removes_piece_from_a_board_at_given_position(Board $board)
    {
        $piece = Pawn::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);

        $board->removePieceFrom($position)->shouldBeCalled()->willReturn($piece);

        $this->removePieceFromBoard($position)->shouldBe($piece);
    }
}