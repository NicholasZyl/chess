<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain;

use NicholasZyl\Chess\Domain\Event\PieceWasMoved;
use NicholasZyl\Chess\Domain\Fide\Board\CoordinatePair;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\AlongFile;
use NicholasZyl\Chess\Domain\Fide\Board\Direction\Forward;
use NicholasZyl\Chess\Domain\Fide\Move\NotIntervened;
use NicholasZyl\Chess\Domain\Fide\Piece\Pawn;
use NicholasZyl\Chess\Domain\Piece\Color;
use NicholasZyl\Chess\Domain\Rules;
use PhpSpec\ObjectBehavior;

class RulesSpec extends ObjectBehavior
{
    function let(Rules\PieceMoves $pieceMoves)
    {
        $this->beConstructedWith([$pieceMoves,]);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType(Rules::class);
    }

    function it_applies_proper_rules_for_piece_move(Rules\PieceMoves $pieceMoves)
    {
        $pawn = Pawn::forColor(Color::white());
        $source = CoordinatePair::fromFileAndRank('c', 2);
        $destination = CoordinatePair::fromFileAndRank('c', 3);
        $move = new NotIntervened($source, $destination, new Forward(Color::white(), new AlongFile()));

        $pieceMoves->isApplicableFor($pawn)->willReturn(true);
        $pieceMoves->mayMove($pawn, $move)->shouldBeCalled();

        $this->mayMove($pawn, $move);
    }

    function it_applies_rules_after_event_happened(Rules\PieceMoves $pieceMoves)
    {
        $event = new PieceWasMoved(
            Pawn::forColor(Color::white()),
            CoordinatePair::fromFileAndRank('b', 2),
            CoordinatePair::fromFileAndRank('b', 3)
        );

        $pieceMoves->applyAfter($event)->shouldBeCalled();

        $this->applyAfter($event);
    }
}
