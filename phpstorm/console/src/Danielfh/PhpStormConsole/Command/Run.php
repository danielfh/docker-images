<?php

namespace Danielfh\PhpStormConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    protected function configure()
    {
        $this->setName('run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phpStormUser = getenv('PHPSTORM_USER');

        if (!file_exists('/opt/danielfh/phpstorm/src/bin/phpstorm.sh')) {
            $upgradeCommand = $this->getApplication()->find('upgrade');
            $upgradeCommand->run(new ArrayInput([]), $output);
        }

        exec("usermod -u {$phpStormUser} phpstorm");
        exec("su -c /opt/danielfh/phpstorm/src/bin/phpstorm.sh phpstorm");
    }
}