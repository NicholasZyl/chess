<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\BoardMove;
use NicholasZyl\Chess\Domain\Exception\Move\NotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\MoveOverInterveningPiece;
use NicholasZyl\Chess\Domain\Exception\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;

class NotIntervenedSpec extends ObjectBehavior
{
    function it_is_chess_move()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new AlongFile();
        $this->beConstructedWith($source, $destination, $direction);

        $this->shouldBeAnInstanceOf(BoardMove::class);
    }

    function it_knows_source_destination_and_direction()
    {
        $source = CoordinatePair::fromFileAndRank('a', 2);
        $destination = CoordinatePair::fromFileAndRank('a', 3);
        $direction = new AlongFile();
        $this->beConstructedWith($source, $destination, $direction);

        $this->source()->shouldBe($source);
        $this->destination()->shouldBe($destination);
        $this->direction()->shouldBe($direction);
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
}
