<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotIntervenedSpec extends ObjectBehavior
{
    function let(Board $board, PieceMoves $pieceMoves)
    {
        $board->verifyThatPositionIsUnoccupied(Argument::cetera())->willReturn();
        $pieceMoves->areApplicableFor(Argument::any())->willReturn(true);
    }

    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new AlongFile();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldBeAnInstanceOf(Move::class);
    }

    function it_validates_if_coordinates_are_on_same_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongFile();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldThrow(new CoordinatesNotReachable($source, $destination, $direction))->duringInstantiation();
    }

    function it_knows_source_and_destination()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new AlongFile();
        $this->beConstructedWith($source, $destination, $direction);

        $this->source()->shouldBe($source);
        $this->destination()->shouldBe($destination);
    }

    function it_is_in_given_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongDiagonal();
        $this->beConstructedWith($source, $destination, $direction);

        $this->inDirection(new AlongDiagonal())->shouldBe(true);
    }

    function it_is_not_in_different_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongDiagonal();
        $this->beConstructedWith($source, $destination, $direction);

        $this->inDirection(new AlongFile())->shouldBe(false);
    }

    function it_moves_piece_from_one_square_to_another(Board $board, PieceMoves $pieceMoves)
    {
        $bishop = Bishop::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $this->beConstructedWith($source, $destination, new AlongDiagonal());

        $board->pickPieceFromCoordinates($source)->willReturn($bishop);
        $board->placePieceAtCoordinates($bishop, $destination)->shouldBeCalled();

        $pieceMoves->mayMove($bishop, $this->getWrappedObject())->shouldBeCalled();

        $this->play($board, new Rules([$pieceMoves->getWrappedObject(),]))->shouldBeLike(
            [
                new PieceWasMoved($bishop, $source, $destination),
            ]
        );
    }

    function it_does_not_allow_moving_over_intervening_pieces(Board $board, PieceMoves $pieceMoves)
    {
        $bishop = Bishop::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 4);
        $this->beConstructedWith($source, $destination, new AlongDiagonal());

        $board->pickPieceFromCoordinates($source)->shouldNotBeCalled();
        $interveningPiecePosition = CoordinatePair::fromFileAndRank('b', 3);
        $board->verifyThatPositionIsUnoccupied($interveningPiecePosition)->willThrow(new SquareIsOccupied($interveningPiecePosition));
        $board->placePieceAtCoordinates($bishop, $destination)->shouldNotBeCalled();

        $pieceMoves->mayMove($bishop, $this->getWrappedObject())->shouldNotBeCalled();

        $this->shouldThrow(new MoveOverInterveningPiece($interveningPiecePosition))->during('play', [$board, new Rules([$pieceMoves->getWrappedObject(),]),]);
    }

    function it_does_not_allow_move_that_is_not_possible_for_given_piece(Board $board, PieceMoves $pieceMoves)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $board->pickPieceFromCoordinates($source)->willReturn($pawn);
        $board->placePieceAtCoordinates($pawn, $source)->shouldBeCalled();

        $moveNotAllowedForPiece = new MoveNotAllowedForPiece($pawn, $this->getWrappedObject());
        $pieceMoves->mayMove($pawn, $this->getWrappedObject())->shouldBeCalled()->willThrow($moveNotAllowedForPiece);

        $this->shouldThrow($moveNotAllowedForPiece)->during('play', [$board, new Rules([$pieceMoves->getWrappedObject(),]),]);
    }

    function it_does_not_allow_moving_to_square_occupied_by_same_color(Board $board, PieceMoves $pieceMoves)
    {
        $rook = Rook::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $board->pickPieceFromCoordinates($source)->willReturn($rook);
        $board->placePieceAtCoordinates($rook, $destination)->willThrow(new SquareIsOccupied($destination));
        $board->placePieceAtCoordinates($rook, $source)->shouldBeCalled();

        $pieceMoves->mayMove($rook, $this->getWrappedObject())->shouldBeCalled();

        $this->shouldThrow(new MoveToOccupiedPosition($destination))->during('play', [$board, new Rules([$pieceMoves->getWrappedObject(),]),]);
    }
}
