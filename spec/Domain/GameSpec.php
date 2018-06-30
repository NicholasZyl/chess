<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Action\Exchange;
use NicholasZyl\Chess\Domain\Action\Move;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\PieceWasExchanged;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\PositionOccupiedByAnotherColor;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ExchangeIsNotAllowed;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\NoApplicableRule;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
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
        $rules->applyRulesTo($move, $board)->shouldBeCalled();

        $board->placePieceAt($pawn, $destination)->shouldBeCalled()->willReturn([]);
        $rules->applyAfter(new PieceWasMoved($move), $board)->shouldBeCalled();

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
        $rules->applyRulesTo($move, $board)->shouldBeCalled()->willThrow($illegalMove);
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
        $rules->applyRulesTo($move, $board)->willThrow(new NoApplicableRule());
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
        $rules->applyRulesTo($move, $board)->shouldBeCalled();
        $board->placePieceAt($pawn, $destination)->shouldBeCalled()->willReturn([]);
        $anotherEvent = new PieceWasMoved(new Move(Rook::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 3), $source));
        $rules->applyAfter(new PieceWasMoved($move), $board)->shouldBeCalled()->willReturn([$anotherEvent]);

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
        $rules->applyRulesTo($move, $board)->shouldBeCalled();
        $board->placePieceAt($pawn, $destination)->shouldBeCalled()->willThrow(new SquareIsOccupied($destination));
        $board->placePieceAt($pawn, $source)->shouldBeCalled();
        $rules->applyAfter(new PieceWasMoved($move), $board)->shouldNotBeCalled();

        $this->shouldThrow(new MoveToOccupiedPosition($destination))->during('playMove', [$source, $destination,]);
    }

    function it_allows_to_exchange_piece_on_board(Board $board, Rules $rules)
    {
        $piece = Queen::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $action = new Exchange($piece, $position);

        $rules->applyRulesTo($action, $board)->shouldBeCalled();

        $pieceWasExchanged = new PieceWasExchanged(Pawn::forColor(Color::white()), $piece, $position);
        $board->exchangePieceOnPositionTo($position, $piece)->shouldBeCalled()->willReturn([$pieceWasExchanged,]);

        $this->exchangePieceOnBoardTo($position, $piece)->shouldBeLike([$pieceWasExchanged,]);
    }

    function it_is_not_allowed_to_exchange_piece_if_it_failed_on_board(Board $board, Rules $rules)
    {
        $piece = Queen::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $action = new Exchange($piece, $position);

        $rules->applyRulesTo($action, $board)->shouldBeCalled();

        $board->exchangePieceOnPositionTo($position, $piece)->shouldBeCalled()->willThrow(new PositionOccupiedByAnotherColor($position, Color::black()));

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('exchangePieceOnBoardTo', [$position, $piece,]);
    }

    function it_is_not_allowed_to_exchange_piece_if_no_rule_is_applicable(Board $board, Rules $rules)
    {
        $piece = Queen::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);

        $rules->applyRulesTo(new Exchange($piece, $position), $board)->shouldBeCalled()->willThrow(new NoApplicableRule());
        $board->exchangePieceOnPositionTo($position, $piece)->shouldNotBeCalled();

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('exchangePieceOnBoardTo', [$position, $piece,]);
    }

    function it_is_not_allowed_to_exchange_piece_if_rule_does_not_allow(Board $board, Rules $rules)
    {
        $piece = Queen::forColor(Color::white());
        $position = CoordinatePair::fromFileAndRank('a', 1);
        $action = new Exchange($piece, $position);

        $rules->applyRulesTo($action, $board)->shouldBeCalled()->willThrow(new ExchangeIsNotAllowed($position));
        $board->exchangePieceOnPositionTo($position, $piece)->shouldNotBeCalled();

        $this->shouldThrow(ExchangeIsNotAllowed::class)->during('exchangePieceOnBoardTo', [$position, $piece,]);
    }
}
