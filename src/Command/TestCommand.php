<?php

namespace App\Command;

use App\Services\LitemfApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    /** @var LitemfApiService */
    private $litemfApiService;

    public function __construct(LitemfApiService $litemfApiService)
    {
        parent::__construct();
        $this->litemfApiService = $litemfApiService;
    }

    protected function configure()
    {
        $this->setName('app:test') # php bin/console app:test
             ->setDescription('Test command.')
             ->setHelp('This command allows you to run arbitrary code in testing purposes.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->litemfApiService->getCountry();
        
        dump($result); die('ok');
    }
}
