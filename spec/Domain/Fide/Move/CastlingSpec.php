<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\InvalidDirection;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MovePrevented;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece;
use NicholasZyl\Chess\Domain\Piece\Color;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CastlingSpec extends ObjectBehavior
{
    function let(Board $board)
    {
        $board->hasPieceAtCoordinates(Argument::cetera())->willReturn(true);
        $board->isPositionAttackedByOpponentOf(Argument::cetera())->willReturn(false);
        $board->verifyThatPositionIsUnoccupied(Argument::cetera())->willReturn();
    }

    function it_is_chess_move()
    {
        $this->beConstructedWith(
            Color::white(),
            CoordinatePair::fromFileAndRank('e', 8),
            CoordinatePair::fromFileAndRank('c', 8)
        );

        $this->shouldBeAnInstanceOf(Move::class);
    }

    function it_can_be_made_queenside()
    {
        $this->beConstructedWith(
            Color::white(),
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );
    }

    function it_can_be_made_kingside()
    {
        $this->beConstructedWith(
            Color::white(),
            CoordinatePair::fromFileAndRank('e', 1),
            CoordinatePair::fromFileAndRank('c', 1)
        );
    }

    function it_cannot_be_made_not_along_rank()
    {
        $source = CoordinatePair::fromFileAndRank('e', 1);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $this->beConstructedWith(
            Color::white(),
            $source,
            $destination
        );

        $this->shouldThrow(new InvalidDirection($source, $destination, new AlongRank()))->duringInstantiation();
    }

    function it_moves_king_two_squares_towards_rook_and_that_rook_to_the_square_king_crossed_queenside(Board $board)
    {
        $king = King::forColor(Color::white());
        $rook = Rook::forColor(Color::white());
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('c', 1);
        $rookInitialPosition = CoordinatePair::fromFileAndRank('a', 1);
        $rookDestination = CoordinatePair::fromFileAndRank('d', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->pickPieceFromCoordinates($kingInitialPosition)->willReturn($king);
        $board->pickPieceFromCoordinates($rookInitialPosition)->willReturn($rook);
        $board->placePieceAtCoordinates($king, $kingDestination)->shouldBeCalled();
        $board->placePieceAtCoordinates($rook, $rookDestination)->shouldBeCalled();

        $this->play($board);
    }

    function it_moves_king_two_squares_towards_rook_and_that_rook_to_the_square_king_crossed_kingside(Board $board)
    {
        $king = King::forColor(Color::black());
        $rook = Rook::forColor(Color::black());
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 8);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 8);
        $rookInitialPosition = CoordinatePair::fromFileAndRank('h', 8);
        $rookDestination = CoordinatePair::fromFileAndRank('f', 8);
        $this->beConstructedWith(
            Color::black(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->pickPieceFromCoordinates($kingInitialPosition)->willReturn($king);
        $board->pickPieceFromCoordinates($rookInitialPosition)->willReturn($rook);
        $board->placePieceAtCoordinates($king, $kingDestination)->shouldBeCalled();
        $board->placePieceAtCoordinates($rook, $rookDestination)->shouldBeCalled();

        $this->play($board);
    }

    function it_is_prevented_if_there_is_any_piece_between_the_king_and_the_rook(Board $board)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->verifyThatPositionIsUnoccupied($kingDestination)->shouldBeCalled()->willThrow(new SquareIsOccupied($kingDestination));

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board,]);
    }

    function it_is_prevented_if_king_may_not_do_it(Board $board, Piece $king)
    {
        $rook = Rook::forColor(Color::white());
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $rookInitialPosition = CoordinatePair::fromFileAndRank('h', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $king->color()->willReturn(Color::white());
        $king->__toString()->willReturn('king');
        $king->mayMove($this->getWrappedObject(), $board)->willThrow(new MoveNotAllowedForPiece($king->getWrappedObject(), $this->getWrappedObject()));

        $board->pickPieceFromCoordinates($kingInitialPosition)->willReturn($king);
        $board->pickPieceFromCoordinates($rookInitialPosition)->willReturn($rook);
        $board->placePieceAtCoordinates($king, $kingInitialPosition)->shouldBeCalled();
        $board->placePieceAtCoordinates($rook, $rookInitialPosition)->shouldBeCalled();

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board,]);
    }

    function it_is_prevented_if_rook_may_not_do_it(Board $board, Piece $rook)
    {
        $king = King::forColor(Color::white());
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $rookInitialPosition = CoordinatePair::fromFileAndRank('h', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $rook->color()->willReturn(Color::white());
        $rook->__toString()->willReturn('rook');
        $rook->mayMove($this->getWrappedObject(), $board)->willThrow(new MoveNotAllowedForPiece($rook->getWrappedObject(), $this->getWrappedObject()));

        $board->pickPieceFromCoordinates($kingInitialPosition)->willReturn($king);
        $board->pickPieceFromCoordinates($rookInitialPosition)->willReturn($rook);
        $board->placePieceAtCoordinates($king, $kingInitialPosition)->shouldBeCalled();
        $board->placePieceAtCoordinates($rook, $rookInitialPosition)->shouldBeCalled();

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board,]);
    }

    function it_cannot_be_done_if_there_is_no_king_at_source_position(Board $board)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->hasPieceAtCoordinates(King::forColor(Color::white()), $kingInitialPosition)->willReturn(false);

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board,]);
    }

    function it_cannot_be_done_if_there_is_no_rook_at_expected_position(Board $board)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 8);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 8);
        $rookInitialPosition = CoordinatePair::fromFileAndRank('h', 8);
        $this->beConstructedWith(
            Color::black(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->hasPieceAtCoordinates(King::forColor(Color::black()), $kingInitialPosition)->willReturn(true);
        $board->hasPieceAtCoordinates(Rook::forColor(Color::black()), $rookInitialPosition)->willReturn(false);

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board,]);
    }

    function it_is_temporarily_prevented_if_the_square_on_which_king_stands_is_attacked_by_opponents_piece(Board $board)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->isPositionAttackedByOpponentOf($kingInitialPosition, Color::white())->willReturn(true);

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board,]);
    }

    function it_is_temporarily_prevented_if_the_square_which_king_must_cross_is_attacked_by_opponents_piece(Board $board)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('c', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->isPositionAttackedByOpponentOf($kingInitialPosition, Color::white())->willReturn(false);
        $board->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('d', 1), Color::white())->willReturn(true);

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board,]);
    }

    function it_is_temporarily_prevented_if_the_square_which_king_is_to_occupy_is_attacked_by_opponents_piece(Board $board)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->isPositionAttackedByOpponentOf($kingInitialPosition, Color::white())->willReturn(false);
        $board->isPositionAttackedByOpponentOf(CoordinatePair::fromFileAndRank('g', 1), Color::white())->willReturn(true);

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board,]);
    }
}
