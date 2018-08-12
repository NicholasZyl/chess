<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Console\Command;

use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\GameId;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class PlayGameCommand extends Command
{
    public const NAME = 'play';

    /**
     * @var GameService
     */
    private $gameService;

    /**
     * @var GameId
     */
    private $gameId;

    /**
     * Create a command to play the game interactively.
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
        $this->setDescription('Play the game interactively, this is the default entrypoint');
        $this->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'Game identifier');
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        if ($identifier = $input->getOption('id')) {
            $this->gameId = new GameId($identifier);
        }
    }


    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$this->gameId) {
            /** @var QuestionHelper $helper */
            $helper = $this->getHelper('question');
            if ($helper->ask($input, $output, new ConfirmationQuestion('Do you want to start a new game?'))) {
                $command = $this->getApplication()->find(SetupGameCommand::NAME);
                $tempOutput = new BufferedOutput();
                $command->run($input, $tempOutput);
                $this->gameId = new GameId($tempOutput->fetch());
                $output->writeln(sprintf('<info>Game was started: %s</info>', $this->gameId->id()));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->gameId) {
            return 0;
        }

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $action = $helper->ask($input, $output, new ChoiceQuestion('What do you want to do?', [
            DisplayGameStateCommand::NAME,
            MoveCommand::NAME,
            ExchangeCommand::NAME,
            'exit'
        ]));
        if ($action === 'exit') {
            return 0;
        }

        $command = $this->getApplication()->find($action);
        $commandInput = new ArrayInput(['--id' => $this->gameId->id(),]);
        $command->run($commandInput, $output);

        return $this->run($input, $output);
    }
}