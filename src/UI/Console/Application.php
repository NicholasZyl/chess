<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\HttpKernel\KernelInterface;

final class Application extends BaseApplication
{
    /**
     * Create an instance of the chess application.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct('Chess', '1.0');
        $kernel->boot();
        $container = $kernel->getContainer();
        if ($container->hasParameter('console.command.ids')) {
            foreach ($container->getParameter('console.command.ids') as $id) {
                $this->add($container->get($id));
            }
        }
    }
}
