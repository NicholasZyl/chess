<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Chessboard\Move;

use NicholasZyl\Chess\Domain\Chessboard\Move\Path;
use NicholasZyl\Chess\Domain\Chessboard\Move\PathPlanner;
use NicholasZyl\Chess\Domain\Chessboard\Square\CoordinatePair;
use PhpSpec\ObjectBehavior;

class PathPlannerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PathPlanner::class);
    }

    function it_allows_planning_a_step_to_the_square()
    {
        $this->step(CoordinatePair::fromFileAndRank('a', 2));
    }

    function it_has_fluent_interface()
    {
        $this->step(CoordinatePair::fromFileAndRank('a', 2))->shouldBe($this->getWrappedObject());
    }

    function it_plans_final_path()
    {
        $interveningSquare = CoordinatePair::fromFileAndRank('a', 2);
        $finalSquare = CoordinatePair::fromFileAndRank('a', 3);

        $this->step($interveningSquare);
        $this->step($finalSquare);

        $this->plan()->shouldBeLike(Path::forSquares([$interveningSquare, $finalSquare,]));
    }
}
