<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Web\EventListener;

use NicholasZyl\Chess\Domain\Exception\GameNotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

final class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        if ($exception instanceof GameNotFound) {
            $code = Response::HTTP_NOT_FOUND;
        } else {
            $code = Response::HTTP_BAD_REQUEST;
        }

        $event->setResponse(
            JsonResponse::create(
                [
                    'message' => $exception->getMessage(),
                ],
                $code
            )
        );
    }
}
