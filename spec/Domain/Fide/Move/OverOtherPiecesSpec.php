<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToOccupiedPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped;
use NicholasZyl\Chess\Domain\Fide\Piece\Knight;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OverOtherPiecesSpec extends ObjectBehavior
{
    function let(Board $board)
    {
        $board->verifyThatPositionIsUnoccupied(Argument::cetera())->willReturn();
    }

    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new LShaped();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldBeAnInstanceOf(Move::class);
    }

    function it_validates_if_coordinates_are_on_same_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new LShaped();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldThrow(new CoordinatesNotReachable($source, $destination, $direction))->duringInstantiation();
    }

    function it_knows_source_and_destination()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $direction = new LShaped();
        $this->beConstructedWith($source, $destination, $direction);

        $this->source()->shouldBe($source);
        $this->destination()->shouldBe($destination);
    }

    function it_is_in_given_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $direction = new LShaped();
        $this->beConstructedWith($source, $destination, $direction);

        $this->inDirection(new LShaped())->shouldBe(true);
    }

    function it_is_not_in_different_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $direction = new LShaped();
        $this->beConstructedWith($source, $destination, $direction);

        $this->inDirection(new AlongDiagonal())->shouldBe(false);
    }

    function it_moves_piece_from_one_square_to_another(Board $board, PieceMoves $knightMoves)
    {
        $knight = Knight::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $this->beConstructedWith($source, $destination, new LShaped());

        $board->pickPieceFromCoordinates($source)->willReturn($knight);
        $board->placePieceAtCoordinates($knight, $destination)->shouldBeCalled();

        $knightMoves->isApplicableFor($knight)->willReturn(true);
        $knightMoves->mayMove($knight, $this->getWrappedObject())->shouldBeCalled();

        $this->play($board, new Rules([$knightMoves->getWrappedObject(),]))->shouldBeLike(
            [
                new PieceWasMoved($knight, $source, $destination),
            ]
        );
    }

    function it_does_not_allow_moving_to_square_occupied_by_same_color(Board $board, PieceMoves $knightMoves)
    {
        $knight = Knight::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $this->beConstructedWith($source, $destination, new LShaped());

        $board->pickPieceFromCoordinates($source)->willReturn($knight);
        $board->placePieceAtCoordinates($knight, $destination)->willThrow(new SquareIsOccupied($destination));
        $board->placePieceAtCoordinates($knight, $source)->shouldBeCalled();

        $knightMoves->isApplicableFor($knight)->willReturn(true);
        $knightMoves->mayMove($knight, $this->getWrappedObject())->shouldBeCalled();

        $this->shouldThrow(new MoveToOccupiedPosition($destination))->during('play', [$board, new Rules([$knightMoves->getWrappedObject(),]),]);
    }

    function it_does_not_allow_move_that_is_not_possible_for_given_piece(Board $board, PieceMoves $knightMoves)
    {
        $knight = Knight::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $board->pickPieceFromCoordinates($source)->willReturn($knight);
        $board->placePieceAtCoordinates($knight, $source)->shouldBeCalled();

        $moveNotAllowedForPiece = new MoveNotAllowedForPiece($knight, $this->getWrappedObject());
        $knightMoves->isApplicableFor($knight)->willReturn(true);
        $knightMoves->mayMove($knight, $this->getWrappedObject())->willThrow($moveNotAllowedForPiece);

        $this->shouldThrow($moveNotAllowedForPiece)->during('play', [$board, new Rules([$knightMoves->getWrappedObject(),]),]);
    }
}
