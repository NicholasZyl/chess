<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide;

use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoardCoordinates;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ChessboardSpec extends ObjectBehavior
{
    function let(Rules\PieceMoves $pieceMoves)
    {
        $this->beConstructedWith(new Rules([$pieceMoves->getWrappedObject(),]));
        $pieceMoves->areApplicableFor(Argument::any())->willReturn(true);
    }

    function it_is_composed_of_sixty_four_squares()
    {
        $coordinates = CoordinatePair::fromFileAndRank('h', 8);
        $this->verifyThatPositionIsUnoccupied($coordinates);
    }

    function it_knows_when_no_events_occurred()
    {
        $this->occurredEvents()->shouldBe([]);
    }

    function it_does_not_allow_interacting_with_position_out_of_board()
    {
        $coordinates = CoordinatePair::fromFileAndRank('i', 9);

        $this->shouldThrow(new OutOfBoardCoordinates($coordinates))->during('verifyThatPositionIsUnoccupied', [$coordinates,]);
    }

    function it_allows_placing_piece_at_given_coordinates()
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());
        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAtCoordinates($whitePawn, $coordinates);
    }

    function it_knows_when_opponents_piece_was_captured()
    {
        $whiteRook = Rook::forColor(Piece\Color::white());
        $blackPawn = Pawn::forColor(Piece\Color::black());

        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAtCoordinates($blackPawn, $coordinates);
        $this->placePieceAtCoordinates($whiteRook, $coordinates);

        $this->occurredEvents()->shouldBeLike([new PieceWasCaptured($blackPawn, $coordinates),]);
    }

    function it_does_not_allow_placing_piece_on_coordinates_occupied_by_same_color()
    {
        $whiteRook = Rook::forColor(Piece\Color::white());
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAtCoordinates($whiteRook, $coordinates);

        $this->shouldThrow(SquareIsOccupied::class)->during('placePieceAtCoordinates', [$whitePawn, $coordinates,]);
    }

    function it_allows_picking_up_piece_from_coordinates()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);
        $pawn = Pawn::forColor(Piece\Color::white());
        $this->placePieceAtCoordinates($pawn, $position);

        $this->pickPieceFromCoordinates($position)->shouldBe($pawn);
    }

    function it_does_not_allow_picking_a_piece_from_unoccupied_position()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);

        $this->shouldThrow(new SquareIsUnoccupied($position))->during('pickPieceFromCoordinates', [$position,]);
    }

    function it_allows_moving_piece_if_move_is_legal(Rules\PieceMoves $pieceMoves)
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);

        $this->placePieceAtCoordinates($whitePawn, $source);

        $pieceMoves->mayMove($whitePawn, new NotIntervened($source, $destination, new Forward(Piece\Color::white(), new AlongFile())))->shouldBeCalled();

        $this->movePiece($source, $destination);

        $this->occurredEvents()->shouldBeLike([new PieceWasMoved($whitePawn, $source, $destination),]);
    }

    function it_does_not_allow_move_to_illegal_position(Rules\PieceMoves $pieceMoves)
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 1);

        $this->placePieceAtCoordinates($whitePawn, $source);

        $pieceMoves->mayMove(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(new MoveToIllegalPosition($whitePawn, $source, $destination))->during('movePiece', [$source, $destination,]);
        $this->occurredEvents()->shouldBe([]);
    }

    function it_does_not_allow_illegal_move(Rules\PieceMoves $pieceMoves)
    {
        $whitePawn = Pawn::forColor(Piece\Color::white());

        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 5);

        $this->placePieceAtCoordinates($whitePawn, $source);

        $move = new NotIntervened($source, $destination, new Forward(Piece\Color::white(), new AlongFile()));
        $illegalMove = new MoveNotAllowedForPiece($whitePawn, $move);
        $pieceMoves->mayMove($whitePawn, $move)->willThrow($illegalMove);

        $this->shouldThrow($illegalMove)->during('movePiece', [$source, $destination,]);
        $this->occurredEvents()->shouldBe([]);
    }

    function it_fails_when_trying_to_move_from_not_occupied_coordinates()
    {
        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 1);

        $this->shouldThrow(new SquareIsUnoccupied($source))->during('movePiece', [$source, $destination,]);
        $this->occurredEvents()->shouldBe([]);
    }

    function it_collects_all_events_happened_during_the_move(Rules\PieceMoves $pieceMoves)
    {
        $whiteRook = Rook::forColor(Piece\Color::white());
        $blackPawn = Pawn::forColor(Piece\Color::black());

        $source = CoordinatePair::fromFileAndRank('b', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);

        $this->placePieceAtCoordinates($whiteRook, $source);
        $this->placePieceAtCoordinates($blackPawn, $destination);

        $pieceMoves->mayMove($whiteRook, new NotIntervened($source, $destination, new AlongFile()))->shouldBeCalled();

        $this->movePiece($source, $destination);

        $this->occurredEvents()->shouldBeLike([new PieceWasCaptured($blackPawn, $destination), new PieceWasMoved($whiteRook, $source, $destination),]);
    }

    function it_knows_when_square_at_given_coordinates_is_unoccupied()
    {
        $this->verifyThatPositionIsUnoccupied(CoordinatePair::fromFileAndRank('a', 1));
    }

    function it_knows_when_square_at_given_coordinates_is_occupied()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);
        $this->placePieceAtCoordinates(
            Pawn::forColor(Piece\Color::white()),
            $position
        );

        $this->shouldThrow(SquareIsOccupied::class)->during('verifyThatPositionIsUnoccupied', [$position,]);
    }

    function it_knows_when_square_at_coordinates_is_occupied_by_opponent_if_piece_has_different_color()
    {
        $blackPawn = Pawn::forColor(Piece\Color::black());
        $position = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAtCoordinates($blackPawn, $position);

        $this->hasOpponentsPieceAt($position, Piece\Color::white())->shouldBe(true);
    }

    function it_knows_when_square_at_coordinates_is_not_occupied_by_opponent_if_piece_has_same_color()
    {
        $blackPawn = Pawn::forColor(Piece\Color::black());
        $position = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAtCoordinates($blackPawn, $position);

        $this->hasOpponentsPieceAt($position, Piece\Color::black())->shouldBe(false);
    }

    function it_knows_when_square_at_coordinates_is_not_occupied_by_opponent_if_piece_is_unoccupied()
    {
        $position = CoordinatePair::fromFileAndRank('c', 4);

        $this->hasOpponentsPieceAt($position, Piece\Color::black())->shouldBe(false);
    }

    function it_knows_when_square_is_attacked_by_opponents_piece()
    {
        $this->placePieceAtCoordinates(Pawn::forColor(Piece\Color::black()), CoordinatePair::fromFileAndRank('c', 4));
        $this->placePieceAtCoordinates(Pawn::forColor(Piece\Color::white()), CoordinatePair::fromFileAndRank('b', 3));

        $this->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('b', 3), Piece\Color::white())->shouldBe(true);
    }

    function it_knows_when_square_is_not_attacked_by_opponents_piece()
    {
        $this->placePieceAtCoordinates(Pawn::forColor(Piece\Color::black()), CoordinatePair::fromFileAndRank('a', 3));
        $this->placePieceAtCoordinates(Rook::forColor(Piece\Color::black()), CoordinatePair::fromFileAndRank('a', 8));
        $this->placePieceAtCoordinates(Bishop::forColor(Piece\Color::white()), CoordinatePair::fromFileAndRank('b', 3));

        $this->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('a', 2), Piece\Color::white())->shouldBe(false);
    }

    function it_cannot_check_if_square_out_of_board_is_attacked()
    {
        $coordinates = CoordinatePair::fromFileAndRank('z', 9);

        $this->shouldThrow(new OutOfBoardCoordinates($coordinates))->during('isPositionAttackedByOpponentOf', [$coordinates, Piece\Color::white()]);
    }
}
