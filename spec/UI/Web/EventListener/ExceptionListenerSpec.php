<?php
declare(strict_types=1);

namespace spec\NicholasZyl\Chess\UI\Web\EventListener;

use NicholasZyl\Chess\Domain\Exception\GameNotFound;
use NicholasZyl\Chess\Domain\Exception\IllegalAction\ActionNotAllowed;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\UI\Web\EventListener\ExceptionListener;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ExceptionListener::class);
    }

    function it_returns_json_response_with_not_found_code_and_error_message_on_game_not_found_exception(GetResponseForExceptionEvent $event)
    {
        $exception = new GameNotFound(new GameId('id'));
        $event->getException()->willReturn($exception);

        $event->setResponse(JsonResponse::create(['message' => $exception->getMessage(),], Response::HTTP_NOT_FOUND))->shouldBeCalled();

        $this->onKernelException($event);
    }

    function it_returns_json_response_with_bad_request_code_and_error_message_on_domain_exception(GetResponseForExceptionEvent $event)
    {
        $exception = new ActionNotAllowed('thing');
        $event->getException()->willReturn($exception);

        $event->setResponse(JsonResponse::create(['message' => $exception->getMessage(),], Response::HTTP_BAD_REQUEST))->shouldBeCalled();

        $this->onKernelException($event);
    }
}
