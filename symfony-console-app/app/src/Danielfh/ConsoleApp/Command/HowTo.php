<?php
namespace Danielfh\ConsoleApp\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HowTo extends Command
{
    protected function configure()
    {
        $this->setName('how-to-use-this-image');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('How to use this image');
        $io->listing([
            "Make a copy of your code and put it inside /usr/local/share/danielfh/console/app/src",
            "Add a loader in /usr/local/share/danielfh/console/loader.php and include your bootstrap",
            "Put your composer.json in /usr/share/danielfh/console/composer.json with your namespace",
        ]);
        
        $io->title('Example');
        $io->block([
            "ADD /MyCode /usr/local/share/danielfh/console/app/src",
            "ADD MyLoader.php /usr/local/share/danielfh/console/loader.php",
            "RUN /usr/bin/composer install",
        ]);
    }
}