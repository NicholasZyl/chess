<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\Domain\Rules;

use NicholasZyl\Chess\Domain\Action;
use NicholasZyl\Chess\Domain\Board;
use NicholasZyl\Chess\Domain\Color;
use NicholasZyl\Chess\Domain\Event\Checkmated;
use NicholasZyl\Chess\Domain\Event\GameEnded;
use NicholasZyl\Chess\Domain\Event\Stalemate;
use NicholasZyl\Chess\Domain\Rule;
use NicholasZyl\Chess\Domain\Rules;
use NicholasZyl\Chess\Domain\Rules\GameCompletion;
use PhpSpec\ObjectBehavior;

class GameCompletionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(GameCompletion::class);
    }

    function it_is_chess_rule()
    {
        $this->shouldBeAnInstanceOf(Rule::class);
    }

    function it_is_not_applicable_to_actions(Action $action)
    {
        $this->isApplicableTo($action)->shouldBe(false);
    }

    function it_ends_game_on_checkmate_and_announces_attacking_player_winner(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new Checkmated(Color::white()),
            $board,
            $rules
        )->shouldBeLike([new GameEnded(Color::black()),]);
    }

    function it_ends_game_with_a_drawn_on_a_stalemate(Board $board, Rules $rules)
    {
        $this->applyAfter(
            new Stalemate(),
            $board,
            $rules
        )->shouldBeLike([new GameEnded(),]);
    }
}
