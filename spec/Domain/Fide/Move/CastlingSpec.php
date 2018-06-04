<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Move;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Exception\Board\CoordinatesNotReachable;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MovePrevented;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Fide\Piece\King;
use NicholasZyl\Chess\Domain\Fide\Piece\Rook;
use NicholasZyl\Chess\Domain\Move;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\PieceMoves;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CastlingSpec extends ObjectBehavior
{
    function let(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
    {
        $board->isPositionAttackedByOpponentOf(Argument::cetera())->willReturn(false);
        $board->verifyThatPositionIsUnoccupied(Argument::cetera())->willReturn();

        $kingMoves->areApplicableFor(Argument::type(King::class))->willReturn(true);
        $kingMoves->areApplicableFor(Argument::type(Rook::class))->willReturn(false);
        $rookMoves->areApplicableFor(Argument::type(Rook::class))->willReturn(true);
        $rookMoves->areApplicableFor(Argument::type(King::class))->willReturn(false);
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

        $this->shouldThrow(new CoordinatesNotReachable($source, $destination, new AlongRank()))->duringInstantiation();
    }

    function it_moves_king_two_squares_towards_rook_and_that_rook_to_the_square_king_crossed_queenside(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
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

        $kingMoves->mayMove($king, $this->getWrappedObject())->shouldBeCalled();
        $rookMoves->mayMove($rook, $this->getWrappedObject())->shouldBeCalled();

        $this->play($board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]))->shouldBeLike(
            [
                new PieceWasMoved($king, $kingInitialPosition, $kingDestination),
                new PieceWasMoved($rook, $rookInitialPosition, $rookDestination),
            ]
        );
    }

    function it_moves_king_two_squares_towards_rook_and_that_rook_to_the_square_king_crossed_kingside(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
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

        $kingMoves->mayMove($king, $this->getWrappedObject())->shouldBeCalled();
        $rookMoves->mayMove($rook, $this->getWrappedObject())->shouldBeCalled();

        $this->play($board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]))->shouldBeLike(
            [
                new PieceWasMoved($king, $kingInitialPosition, $kingDestination),
                new PieceWasMoved($rook, $rookInitialPosition, $rookDestination),
            ]
        );
    }

    function it_is_prevented_if_there_is_any_piece_between_the_king_and_the_rook(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->verifyThatPositionIsUnoccupied($kingDestination)->shouldBeCalled()->willThrow(new SquareIsOccupied($kingDestination));

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]),]);
    }

    function it_is_prevented_if_king_may_not_do_it(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
    {
        $king = King::forColor(Color::white());
        $rook = Rook::forColor(Color::white());
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $rookInitialPosition = CoordinatePair::fromFileAndRank('h', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->pickPieceFromCoordinates($kingInitialPosition)->willReturn($king);
        $board->pickPieceFromCoordinates($rookInitialPosition)->willReturn($rook);
        $board->placePieceAtCoordinates($king, $kingInitialPosition)->shouldBeCalled();
        $board->placePieceAtCoordinates($rook, $rookInitialPosition)->shouldBeCalled();

        $kingMoves->mayMove($king, $this->getWrappedObject())->shouldBeCalled()->willThrow(new MoveNotAllowedForPiece($king, $this->getWrappedObject()));
        $rookMoves->mayMove($rook, $this->getWrappedObject())->shouldNotBeCalled();

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]),]);
    }

    function it_is_prevented_if_rook_may_not_do_it(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
    {
        $king = King::forColor(Color::white());
        $rook = Rook::forColor(Color::white());
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $rookInitialPosition = CoordinatePair::fromFileAndRank('h', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->pickPieceFromCoordinates($kingInitialPosition)->willReturn($king);
        $board->pickPieceFromCoordinates($rookInitialPosition)->willReturn($rook);
        $board->placePieceAtCoordinates($king, $kingInitialPosition)->shouldBeCalled();
        $board->placePieceAtCoordinates($rook, $rookInitialPosition)->shouldBeCalled();

        $kingMoves->mayMove($king, $this->getWrappedObject())->shouldBeCalled();
        $rookMoves->mayMove($rook, $this->getWrappedObject())->shouldBeCalled()->willThrow(new MoveNotAllowedForPiece($rook, $this->getWrappedObject()));

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]),]);
    }

    function it_is_temporarily_prevented_if_the_square_on_which_king_stands_is_attacked_by_opponents_piece(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $kingDestination = CoordinatePair::fromFileAndRank('g', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            $kingDestination
        );

        $board->isPositionAttackedByOpponentOf($kingInitialPosition, Color::white())->willReturn(true);

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]),]);
    }

    function it_is_temporarily_prevented_if_the_square_which_king_must_cross_is_attacked_by_opponents_piece(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
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

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]),]);
    }

    function it_is_temporarily_prevented_if_the_square_which_king_is_to_occupy_is_attacked_by_opponents_piece(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
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

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]),]);
    }

    function it_cannot_be_made_if_no_piece_is_placed_at_kings_square(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            CoordinatePair::fromFileAndRank('g', 1)
        );
        $board->pickPieceFromCoordinates($kingInitialPosition)->willThrow(new SquareIsUnoccupied($kingInitialPosition));
        $board->pickPieceFromCoordinates(CoordinatePair::fromFileAndRank('h', 1))->shouldNotBeCalled();

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]),]);
    }

    function it_cannot_be_made_if_no_piece_is_placed_at_rooks_square(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $rookInitialPosition = CoordinatePair::fromFileAndRank('h', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            CoordinatePair::fromFileAndRank('g', 1)
        );

        $king = King::forColor(Color::white());
        $board->pickPieceFromCoordinates($kingInitialPosition)->willReturn($king);
        $board->placePieceAtCoordinates($king, $kingInitialPosition)->shouldBeCalled();
        $board->pickPieceFromCoordinates($rookInitialPosition)->willThrow(new SquareIsUnoccupied($rookInitialPosition));

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]),]);
    }

    function it_cannot_be_made_if_other_piece_is_placed_at_rooks_square(Board $board, PieceMoves $kingMoves, PieceMoves $rookMoves)
    {
        $kingInitialPosition = CoordinatePair::fromFileAndRank('e', 1);
        $rookInitialPosition = CoordinatePair::fromFileAndRank('a', 1);
        $this->beConstructedWith(
            Color::white(),
            $kingInitialPosition,
            CoordinatePair::fromFileAndRank('c', 1)
        );

        $king = King::forColor(Color::white());
        $board->pickPieceFromCoordinates($kingInitialPosition)->willReturn($king);
        $bishop = Bishop::forColor(Color::white());
        $board->pickPieceFromCoordinates($rookInitialPosition)->willReturn($bishop);
        $board->placePieceAtCoordinates($king, $kingInitialPosition)->shouldBeCalled();
        $board->placePieceAtCoordinates($bishop, $rookInitialPosition)->shouldBeCalled();

        $this->shouldThrow(new MovePrevented($this->getWrappedObject()))->during('play', [$board, new Rules([$kingMoves->getWrappedObject(), $rookMoves->getWrappedObject(),]),]);
    }
}
