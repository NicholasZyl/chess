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
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class ExchangeCommand extends Command
{
    public const NAME = 'exchange';

    /**
     * @var GameService
     */
    private $gameService;

    /**
     * Create a command to exchange piece on the board.
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
        $this->setDescription('Exchange a piece in the game');
        $this->addArgument('on', InputArgument::OPTIONAL, 'Coordinates to exchange on');
        $this->addArgument('for', InputArgument::OPTIONAL, 'Piece to exchange to');
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
        if (!$input->getArgument('on')) {
            $on = $helper->ask($input, $output, new Question('Please provide "on" coordinates'));
            $input->setArgument('on', $on);
        }
        if (!$input->getArgument('for')) {
            $for = $helper->ask($input, $output, new ChoiceQuestion('Please choose a piece to exchange for', ['bishop', 'knight', 'rook', 'queen',]));
            $input->setArgument('for', $for);
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
            $this->gameService->exchangePieceInGame($gameId, $input->getArgument('on'), $input->getArgument('for'));
        } catch (IllegalAction | BoardException $exception) {
            $output->writeln('<error>Exchange was not possible</error>');
            $output->writeln(sprintf('<comment>%s</comment>', $exception->getMessage()));

            return 1;
        } catch (GameNotFound $gameNotFound) {
            $output->writeln(sprintf('<error>%s</error>', $gameNotFound->getMessage()));

            return 2;
        }

        return 0;
    }
}