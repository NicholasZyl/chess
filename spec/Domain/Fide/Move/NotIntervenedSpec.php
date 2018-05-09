<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NotIntervenedSpec extends ObjectBehavior
{
    function let(Board $board)
    {
        $board->verifyThatPositionIsUnoccupied(Argument::cetera())->willReturn();
    }

    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new AlongFile();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldBeAnInstanceOf(BoardMove::class);
    }

    function it_validates_if_coordinates_are_on_same_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('b', 3);
        $direction = new AlongFile();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldThrow(new InvalidDirection($source, $destination, $direction))->duringInstantiation();
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

    function it_moves_piece_from_one_square_to_another(Board $board)
    {
        $white = Color::white();
        $pawn = Pawn::forColor($white);
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $this->beConstructedWith($source, $destination, new Forward( $white, new AlongFile()));

        $board->pickPieceFromCoordinates($source)->willReturn($pawn);
        $board->placePieceAtCoordinates($pawn, $destination)->shouldBeCalled();

        $this->play($board);
    }

    function it_does_not_allow_moving_over_intervening_pieces(Board $board)
    {
        $bishop = Bishop::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 4);
        $this->beConstructedWith($source, $destination, new AlongDiagonal());

        $board->pickPieceFromCoordinates($source)->shouldNotBeCalled();
        $interveningPiecePosition = CoordinatePair::fromFileAndRank('b', 3);
        $board->verifyThatPositionIsUnoccupied($interveningPiecePosition)->willThrow(new SquareIsOccupied($interveningPiecePosition));
        $board->placePieceAtCoordinates($bishop, $destination)->shouldNotBeCalled();

        $this->shouldThrow(new MoveOverInterveningPiece($interveningPiecePosition))->during('play', [$board,]);
    }

    function it_does_not_allow_move_that_is_not_possible_for_given_piece(Board $board)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $board->pickPieceFromCoordinates($source)->willReturn($pawn);

        $this->shouldThrow(new NotAllowedForPiece($pawn, $this->getWrappedObject()))->during('play', [$board,]);
    }

    function it_is_same_as_other_not_intervened_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->is(NotIntervened::class)->shouldBe(true);
    }

    function it_is_not_different_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith($source, $destination, new AlongFile());

        $this->is(OverOtherPieces::class)->shouldBe(false);
    }
}
