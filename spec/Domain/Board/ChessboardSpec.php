<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Board;

use NicholasZyl\Chess\Domain\Action\Attack;
use NicholasZyl\Chess\Domain\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Board\Coordinates;
use NicholasZyl\Chess\Domain\Board\Square;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event\PieceWasCaptured;
use NicholasZyl\Chess\Domain\Event\PieceWasExchanged;
use NicholasZyl\Chess\Domain\Exception\Board\OutOfBoard;
use NicholasZyl\Chess\Domain\Exception\Board\PositionOccupiedByAnotherColor;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsUnoccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece\King;
use NicholasZyl\Chess\Domain\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Queen;
use NicholasZyl\Chess\Domain\Piece\Rook;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;

class ChessboardSpec extends ObjectBehavior
{
    function it_is_composed_of_sixty_four_squares()
    {
        $grid = [];
        for ($file = 'a'; $file <= 'h'; ++$file) {
            for ($rank = 1; $rank <= 8; ++$rank) {
                $grid[$file.$rank] = Square::forCoordinates(CoordinatePair::fromFileAndRank($file, $rank));
            }
        }

        $this->grid()->shouldBeLike($grid);
    }

    function it_does_not_allow_interacting_with_position_out_of_board(Coordinates $outOfBoardCoordinates)
    {
        $outOfBoardCoordinates->__toString()->willReturn('');
        $this->shouldThrow(OutOfBoard::class)->during('isPositionOccupied', [$outOfBoardCoordinates,]);
    }

    function it_allows_placing_piece_at_given_coordinates()
    {
        $whitePawn = Pawn::forColor(Color::white());
        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAt($whitePawn, $coordinates)->shouldBeLike([]);
    }

    function it_knows_when_opponents_piece_was_captured()
    {
        $whiteRook = Rook::forColor(Color::white());
        $blackPawn = Pawn::forColor(Color::black());

        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAt($blackPawn, $coordinates);
        $this->placePieceAt($whiteRook, $coordinates)->shouldBeLike(
            [new PieceWasCaptured($blackPawn, $coordinates),]
        );
    }

    function it_does_not_allow_placing_piece_on_coordinates_occupied_by_same_color()
    {
        $whiteRook = Rook::forColor(Color::white());
        $whitePawn = Pawn::forColor(Color::white());

        $coordinates = CoordinatePair::fromFileAndRank('b', 2);

        $this->placePieceAt($whiteRook, $coordinates);

        $this->shouldThrow(SquareIsOccupied::class)->during('placePieceAt', [$whitePawn, $coordinates,]);
    }

    function it_allows_picking_up_piece_from_coordinates()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);
        $pawn = Pawn::forColor(Color::white());
        $this->placePieceAt($pawn, $position);

        $this->pickPieceFrom($position)->shouldBe($pawn);
    }

    function it_does_not_allow_picking_a_piece_from_unoccupied_position()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);

        $this->shouldThrow(new SquareIsUnoccupied($position))->during('pickPieceFrom', [$position,]);
    }

    function it_knows_when_square_at_given_coordinates_is_unoccupied()
    {
        $this->isPositionOccupied(CoordinatePair::fromFileAndRank('a', 1))->shouldBe(false);
    }

    function it_knows_when_square_at_given_coordinates_is_occupied()
    {
        $position = CoordinatePair::fromFileAndRank('a', 2);
        $this->placePieceAt(
            Pawn::forColor(Color::white()),
            $position
        );

        $this->isPositionOccupied($position)->shouldBe(true);
    }

    function it_knows_when_position_is_occupied_by_piece_in_given_color()
    {
        $blackPawn = Pawn::forColor(Color::black());
        $position = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAt($blackPawn, $position);

        $this->isPositionOccupiedBy($position, Color::black())->shouldBe(true);
    }

    function it_knows_when_position_is_not_occupied_by_given_color_if_piece_has_same_color()
    {
        $blackPawn = Pawn::forColor(Color::black());
        $position = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAt($blackPawn, $position);

        $this->isPositionOccupiedBy($position, Color::white())->shouldBe(false);
    }

    function it_knows_when_position_is_not_occupied_by_color_if_piece_is_unoccupied()
    {
        $position = CoordinatePair::fromFileAndRank('c', 4);

        $this->isPositionOccupiedBy($position, Color::black())->shouldBe(false);
    }

    function it_knows_when_square_is_attacked_by_given_color(Rules $rules)
    {
        $position = CoordinatePair::fromFileAndRank('b', 3);
        $opponentsPiece = Pawn::forColor(Color::black());
        $opponentsPiecePosition = CoordinatePair::fromFileAndRank('c', 4);
        $this->placePieceAt($opponentsPiece, $opponentsPiecePosition);
        $this->placePieceAt(Pawn::forColor(Color::white()), $position);

        $rules->applyRulesTo(new Attack($opponentsPiece, $opponentsPiecePosition, $position), $this->getWrappedObject())->shouldBeCalled();

        $this->isPositionAttackedBy($position, Color::black(), $rules)->shouldBe(true);
    }

    function it_knows_when_square_is_not_attacked_by_opponents_piece(Rules $rules)
    {
        $blackPawn = Pawn::forColor(Color::black());
        $blackPawnPosition = CoordinatePair::fromFileAndRank('a', 3);
        $this->placePieceAt($blackPawn, $blackPawnPosition);
        $blackRook = Rook::forColor(Color::black());
        $blackRookPosition = CoordinatePair::fromFileAndRank('a', 8);
        $this->placePieceAt($blackRook, $blackRookPosition);
        $this->placePieceAt(Bishop::forColor(Color::white()), CoordinatePair::fromFileAndRank('b', 3));

        $position = CoordinatePair::fromFileAndRank('a', 2);

        $rules->applyRulesTo(new Attack($blackPawn, $blackPawnPosition, $position), $this->getWrappedObject())->shouldBeCalled()->willThrow(new class extends IllegalAction {});
        $rules->applyRulesTo(new Attack($blackRook, $blackRookPosition, $position), $this->getWrappedObject())->shouldBeCalled()->willThrow(new class extends IllegalAction {});

        $this->isPositionAttackedBy($position, Color::black(), $rules)->shouldBe(false);
    }

    function it_cannot_check_if_square_out_of_board_is_attacked(Coordinates $outOfBoardCoordinates, Rules $rules)
    {
        $outOfBoardCoordinates->__toString()->willReturn('');
        $this->shouldThrow(OutOfBoard::class)->during('isPositionAttackedBy', [$outOfBoardCoordinates, Color::white(), $rules,]);
    }

    function it_removes_piece_from_given_position()
    {
        $position = CoordinatePair::fromFileAndRank('b', 2);
        $piece = Pawn::forColor(Color::white());
        $this->placePieceAt($piece, $position);

        $this->removePieceFrom($position)->shouldBe($piece);

        $this->isPositionOccupied($position)->shouldBe(false);
    }

    function it_cannot_remove_piece_from_unoccupied_square()
    {
        $position = CoordinatePair::fromFileAndRank('b', 2);

        $this->shouldThrow(new SquareIsUnoccupied($position))->during('removePieceFrom', [$position,]);
    }

    function it_exchanges_piece_on_given_position()
    {
        $position = CoordinatePair::fromFileAndRank('b', 2);
        $piece = Pawn::forColor(Color::white());
        $this->placePieceAt($piece, $position);

        $exchangedPiece = Queen::forColor(Color::white());

        $this->exchangePieceOnPositionTo($position, $exchangedPiece)->shouldBeLike([new PieceWasExchanged($piece, $exchangedPiece, $position),]);

        $this->pickPieceFrom($position)->shouldBe($exchangedPiece);
    }

    function it_fails_when_trying_to_exchange_piece_outside_of_board(Coordinates $outOfBoardCoordinates)
    {
        $outOfBoardCoordinates->__toString()->willReturn('');
        $exchangedPiece = Queen::forColor(Color::white());

        $this->shouldThrow(OutOfBoard::class)->during('exchangePieceOnPositionTo', [$outOfBoardCoordinates, $exchangedPiece,]);
    }

    function it_fails_when_trying_to_exchange_on_unoccupied_position()
    {
        $position = CoordinatePair::fromFileAndRank('b', 2);
        $exchangedPiece = Queen::forColor(Color::white());

        $this->shouldThrow(new SquareIsUnoccupied($position))->during('exchangePieceOnPositionTo', [$position, $exchangedPiece,]);
    }

    function it_fails_when_trying_to_exchange_with_another_color()
    {
        $position = CoordinatePair::fromFileAndRank('b', 2);
        $piece = Pawn::forColor(Color::white());
        $this->placePieceAt($piece, $position);

        $exchangedPiece = Queen::forColor(Color::black());

        $this->shouldThrow(PositionOccupiedByAnotherColor::class)->during('exchangePieceOnPositionTo', [$position, $exchangedPiece,]);
    }

    function it_confirms_that_color_has_valid_move_if_any_piece_has_it(Rules $rules)
    {
        $pawn = Pawn::forColor(Color::white());
        $pawnPosition = CoordinatePair::fromFileAndRank('d', 2);
        $this->placePieceAt($pawn, $pawnPosition);
        $king = King::forColor(Color::white());
        $kingPosition = CoordinatePair::fromFileAndRank('b', 4);
        $this->placePieceAt($king, $kingPosition);

        $rules->getLegalDestinationsFrom($pawnPosition, $this->getWrappedObject())->shouldBeCalled()->willReturn([CoordinatePair::fromFileAndRank('d', 3),]);
        $rules->getLegalDestinationsFrom($kingPosition, $this->getWrappedObject())->shouldBeCalled()->willReturn([]);

        $this->hasLegalMove(Color::white(), $rules)->shouldBe(true);
    }
}
