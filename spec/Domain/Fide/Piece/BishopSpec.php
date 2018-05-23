<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Fide\Piece;

use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Exception\Board\SquareIsOccupied;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveNotAllowedForPiece;
use NicholasZyl\Chess\Domain\Exception\IllegalMove\MoveToIllegalPosition;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Move\OverOtherPieces;
use NicholasZyl\Chess\Domain\Fide\Piece\Bishop;
use NicholasZyl\Chess\Domain\Piece;
use PhpSpec\ObjectBehavior;

class BishopSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('forColor', [Piece\Color::white(),]);
    }

    function it_is_a_chess_piece()
    {
        $this->shouldBeAnInstanceOf(Piece::class);
    }

    function it_is_same_as_another_bishop_if_same_color()
    {
        $pawn = Bishop::forColor(Piece\Color::white());

        $this->isSameAs($pawn)->shouldBe(true);
    }

    function it_may_move_to_any_square_along_a_diagonal_on_which_it_stands(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->mayMove($move, $board);
    }

    function it_may_not_move_over_any_intervening_pieces(Board $board)
    {
        $move = new OverOtherPieces(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_along_file(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 1),
            CoordinatePair::fromFileAndRank('a', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_along_rank(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('b', 2),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongRank()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_may_not_move_to_nearest_square(Board $board)
    {
        $move = new NotIntervened(
            CoordinatePair::fromFileAndRank('a', 2),
            CoordinatePair::fromFileAndRank('c', 1),
            new \NicholasZyl\Chess\Domain\Fide\Board\Direction\LShaped()
        );

        $this->shouldThrow(new MoveNotAllowedForPiece($this->getWrappedObject(), $move))->during('mayMove', [$move, $board,]);
    }

    function it_intents_not_intervened_move_along_diagonal()
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('e', 5);

        $this->placeAt($source);
        $this->intentMoveTo($destination)->shouldBeLike(
            new NotIntervened(
                $source,
                $destination,
                new \NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongDiagonal()
            )
        );
    }

    function it_may_not_intent_move_to_illegal_position()
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('a', 5);

        $this->placeAt($source);

        $this->shouldThrow(new MoveToIllegalPosition($this->getWrappedObject(), $source, $destination))->during('intentMoveTo', [$destination,]);
    }

    function it_is_attacking_along_diagonal(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('c', 3);

        $this->placeAt($source);

        $this->isAttacking($destination, $board)->shouldBe(true);
    }

    function it_is_not_attacking_over_other_pieces(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('c', 3);

        $this->placeAt($source);

        $occupiedSquare = CoordinatePair::fromFileAndRank('b', 2);
        $board->verifyThatPositionIsUnoccupied($occupiedSquare)->willThrow(new SquareIsOccupied($occupiedSquare));

        $this->isAttacking($destination, $board)->shouldBe(false);
    }

    function it_is_not_attacking_if_move_is_illegal_for_piece(Board $board)
    {
        $source = CoordinatePair::fromFileAndRank('a', 1);
        $destination = CoordinatePair::fromFileAndRank('c', 2);

        $this->placeAt($source);

        $this->isAttacking($destination, $board)->shouldBe(false);
    }
}
