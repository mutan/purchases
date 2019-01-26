<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class RebuildDatabase extends Command
{
    protected static $defaultName = 'app:rebuild-database';

    protected function configure()
    {
        $this->setDescription('Rebuilds database. Only in development.');
    }

    /**
     * Запускать: php bin/console app:rebuild-database
     *
     * Выполняет последовательно следующие команды:
     * php bin/console doctrine:database:drop --force
     * php bin/console doctrine:database:create
     * php bin/console make:migration
     * php bin/console doctrine:migrations:migrate --no-interaction
     * php bin/console doctrine:fixtures:load --no-interaction
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Команда опасна для продакшена, выполняем только на DEV
        if ('dev' !== getenv('APP_ENV')) {
            $output->writeln(['Command works only on DEV environment! Terminated.']);
            return;
        }

        // Удаляем все существующие файлы миграций
        $fileSystem = new Filesystem();
        $files = glob('src/Migrations/*.php');
        foreach ($files as $file) {
            $fileSystem->remove($file);
        }

        // Выполняем команды
        $commands = [
            ['doctrine:database:drop', ['command' => 'doctrine:database:drop', '--force' => true]],
            ['doctrine:database:create', ['command' => 'doctrine:database:create']],
            ['make:migration', ['command' => 'make:migration']],
            ['doctrine:migrations:migrate', ['command' => 'doctrine:migrations:migrate']],
            ['doctrine:fixtures:load', ['command' => 'doctrine:fixtures:load']],
        ];
        foreach ($commands as $command) {
            $this->executeSubCommand($command[0], $command[1], $output);
        }
    }

    private function executeSubCommand(string $name, array $parameters, OutputInterface $output)
    {
        // Опция --no-interaction не работает, а код ниже работает – во время выполнения не задаюся вопросы
        $input = new ArrayInput($parameters);
        $input->setInteractive(false);

        return $this->getApplication()
                    ->find($name)
                    ->run($input, $output);
    }
}
