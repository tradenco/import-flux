<?php

namespace App\Command;

use App\Service\ImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-data',
    description: 'Add a short description for your command',
)]
class ImportCommand extends Command
{
    public function __construct(
        private readonly ImportService $service
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Début de l\'importation des articles');

        $arg1 = $input->getArgument('arg1');

        $import = $this->service
            ->fromRss('http://www.lemonde.fr/rss/une.xml')
            ->fromJson('https://saurav.tech/NewsAPI/top-headlines/category/health/fr.json')
            ->fromFile('export.json');

        $import->execute();

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            $io->note('You passed option1');
        }

        $io->success('Importation des articles réussie.');

        return Command::SUCCESS;
    }
}
