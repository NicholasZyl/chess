<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Console\Command;

use NicholasZyl\Chess\Application\GameService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupGameCommand extends Command
{
    public const NAME = 'start';

    /**
     * @var GameService
     */
    private $gameService;

    /**
     * Create a command to set up the game.
     *
     * @param GameService $gameService
     */
    public function __construct(GameService $gameService)
    {
        parent::__construct(self::NAME);
        $this->gameService = $gameService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Setup a new game');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gameId = $this->gameService->setupGame();

        $output->writeln($gameId->id());
    }
}