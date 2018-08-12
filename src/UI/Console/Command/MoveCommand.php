<?php
declare(strict_types=1);

namespace NicholasZyl\Chess\UI\Console\Command;

use NicholasZyl\Chess\Application\GameService;
use NicholasZyl\Chess\Domain\Exception\BoardException;
use NicholasZyl\Chess\Domain\Exception\GameNotFound;
use NicholasZyl\Chess\Domain\Exception\IllegalAction;
use NicholasZyl\Chess\Domain\GameId;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MoveCommand extends Command
{
    public const NAME = 'move';

    /**
     * @var GameService
     */
    private $gameService;

    /**
     * Create a command to move a piece in the game.
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
        $this->setDescription('Move a piece in the game');
        $this->addArgument('from', InputArgument::OPTIONAL, 'Coordinates to move from');
        $this->addArgument('to', InputArgument::OPTIONAL, 'Coordinates to move to');
        $this->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'Game identifier');
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        if (!$input->getOption('id')) {
            $identifier = $helper->ask($input, $output, new Question('Please provide the game identifier'));
            $input->setOption('id', $identifier);
        }
        if (!$input->getArgument('from')) {
            $from = $helper->ask($input, $output, new Question('Please provide "from" coordinates'));
            $input->setArgument('from', $from);
        }
        if (!$input->getArgument('to')) {
            $to = $helper->ask($input, $output, new Question('Please provide "to" coordinates'));
            $input->setArgument('to', $to);
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
            $this->gameService->movePieceInGame($gameId, $input->getArgument('from'), $input->getArgument('to'));
        } catch (IllegalAction | BoardException $exception) {
            $output->writeln('<error>Move was not possible</error>');
            $output->writeln(sprintf('<comment>%s</comment>', $exception->getMessage()));

            return 1;
        } catch (GameNotFound $gameNotFound) {
            $output->writeln(sprintf('<error>%s</error>', $gameNotFound->getMessage()));

            return 2;
        }

        return 0;
    }
}