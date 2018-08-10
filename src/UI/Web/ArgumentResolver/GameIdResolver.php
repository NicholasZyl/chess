<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Web\ArgumentResolver;

use NicholasZyl\Chess\Domain\GameId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class GameIdResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $argument->getType() === GameId::class;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield new GameId($request->get('gameId'));
    }
}