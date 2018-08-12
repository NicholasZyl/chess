<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Console;

use NicholasZyl\Chess\UI\Console\Command\DisplayGameStateCommand;
use NicholasZyl\Chess\UI\Console\Command\SetupGameCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
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
        $this->setCommandLoader(
            new ContainerCommandLoader(
                $kernel->getContainer(),
                [
                    'start' => 'console.command.public_alias.'.SetupGameCommand::class,
                    'display' => 'console.command.public_alias.'.DisplayGameStateCommand::class,
                ]
            )
        );
    }
}
