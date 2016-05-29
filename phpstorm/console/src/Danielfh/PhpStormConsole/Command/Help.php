<?php

namespace Danielfh\PhpStormConsole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Help extends Command
{
    protected function configure()
    {
        $this->setName('help');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $symfonyStyle->title('Help');

        $symfonyStyle->block('In order to use this image you must to set the following data:');
        $symfonyStyle->listing([
            'Export your environment DISPLAY var. -e DISPLAY=$DISPLAY',
            'Export the id for the user who wants to execute this app. -e PHPSTORM_USER=$(id -u)',
            'Mount your X11 socket. -v /tmp/.X11-unix:/tmp/.X11-unix',
            'Mount a folder in /opt/danielfh/phpstorm/src where the persistent data will be stored. -v $HOME/phpstorm/:/opt/danielfh/phpstorm/',
            'Mount your project(s) folder in /opt/danielfh/phpstorm/home. -v $HOME:/opt/danielfh/phpstorm/home/',
        ]);

        $symfonyStyle->title('How to upgrade PHPStorm');
        $symfonyStyle->block('You must run "upgrade"');
        $symfonyStyle->block("docker run -t -e DISPLAY -e PHPSTORM_USER=$(id -u) -v /tmp/.X11-unix:/tmp/.X11-unix -v \$HOME/phpstorm/:/opt/danielfh/phpstorm/src/ -v \$HOME:/opt/danielfh/phpstorm/home/ danielfh/phpstorm upgrade");

        $symfonyStyle->title('Example of start command');
        $symfonyStyle->block("docker run -t -e DISPLAY -e PHPSTORM_USER=$(id -u) -v /tmp/.X11-unix:/tmp/.X11-unix -v \$HOME/phpstorm/:/opt/danielfh/phpstorm/src/ -v \$HOME:/opt/danielfh/phpstorm/home/ danielfh/phpstorm");
    }

}
