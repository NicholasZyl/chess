<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Console\Command;

use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\Exception\GameNotFound;
use NicholasZyl\Chess\Domain\GameId;
use NicholasZyl\Chess\UI\Console\AsciiTerminalDisplay;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class DisplayGameStateCommand extends Command
{
    public const NAME = 'display';

    /**
     * @var GameService
     */
    private $gameService;

    /**
     * Create a command to display the current game state.
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
        $this->setDescription('Display the game state');
        $this->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'Game identifier');
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('id')) {
            /** @var QuestionHelper $helper */
            $helper = $this->getHelper('question');
            $identifier = $helper->ask($input, $output, new Question("Please provide the game identifier\n"));
            $input->setOption('id', $identifier);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getOption('id')) {
            $output->writeln('<error>Game identifier is required</error>');

            return 1;
        }

        try {
            $gameId = new GameId($input->getOption('id'));
            $game = $this->gameService->find($gameId);
        } catch (GameNotFound $gameNotFound) {
            $output->writeln(sprintf('<error>%s</error>', $gameNotFound->getMessage()));

            return 2;
        }

        $output->write($game->visualise(new AsciiTerminalDisplay()));

        return 0;
    }
}